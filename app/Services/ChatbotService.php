<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\User;

class ChatbotService
{
    public function __construct(private readonly OpenRouterService $openRouter) {}

    public function respond(string $message, ?User $user = null): array
    {
        $text = strtolower(trim($message));

        if ($text === '' || $text === ' ') {
            return $this->reply('Please type a message so I can help you.', 'text');
        }

        $aiReply = $this->askAi($message, $user);
        if ($aiReply !== null) {
            return $this->reply($aiReply, 'text');
        }

        if ($this->containsAny($text, ['recommend', 'suggest', 'suitable', 'best job', 'jobs for me', 'fit', 'match', 'find a job', 'find job', 'find me'])) {
            return $this->recommendJobs($user);
        }

        if ($this->containsAny($text, ['how to apply', 'how do i apply', 'apply for', 'submitting application', 'application process'])) {
            return $this->reply(
                "To apply for a job:\n\n1. Browse available jobs from the Jobs page.\n2. Open a job you like and click 'Apply Now'.\n3. Optionally add a cover letter.\n4. You can review or cancel your applications anytime from 'My Applications'.\n\nNote: a job can only be applied to once, and only before its deadline.",
                'text'
            );
        }

        if ($this->containsAny($text, ['cancel', 'withdraw', 'remove application'])) {
            return $this->reply(
                "To cancel an application:\n\n1. Go to 'My Applications'.\n2. Find the job you applied to.\n3. Click 'Cancel Application'.\n\nThe application will be removed immediately.",
                'text'
            );
        }

        if ($this->containsAny($text, ['deadline', 'expired', 'open', 'available'])) {
            return $this->deadlineInfo($user);
        }

        if ($this->containsAny($text, ['edit profile', 'update profile', 'profile', 'change my info', 'resume', 'cv', 'upload'])) {
            return $this->reply(
                "You can update your profile from the 'My Profile' page. There you can edit your full name, age, job title, description, phone, skills, profile image and resume (CV).",
                'text'
            );
        }

        if ($this->containsAny($text, ['admin', 'login as admin', 'admin account'])) {
            return $this->reply(
                "Admins log in with their own account using the same login page. Admins can add/edit/delete jobs, view all candidates, and view all job applications.",
                'text'
            );
        }

        if ($this->containsAny($text, ['who are you', 'what are you', 'your name', 'hello', 'hi', 'hey', 'good morning', 'good evening', 'good afternoon'])) {
            return $this->reply(
                "Hello! I'm Jobot, the AI assistant for AI Job Board. I can recommend suitable jobs based on your skills, answer questions about applying, and help you navigate the platform. Try asking: \"recommend me a job\" or \"how to apply?\"",
                'text'
            );
        }

        if ($this->containsAny($text, ['thank', 'thanks', 'great', 'awesome', 'cool'])) {
            return $this->reply("You're welcome! Good luck with your job search. Ask me anytime if you need help.", 'text');
        }

        if ($this->containsAny($text, ['bye', 'goodbye', 'see you'])) {
            return $this->reply("Goodbye! I hope you find your dream job. Come back anytime.", 'text');
        }

        if ($this->containsAny($text, ['count', 'how many', 'number of', 'statistics', 'stats', 'total'])) {
            return $this->stats($user);
        }

        if ($this->containsAny($text, ['job title', 'title', 'category', 'location', 'salary', 'work type', 'remote', 'hybrid', 'onsite', 'on-site'])) {
            return $this->jobInfo($user);
        }

        return $this->reply(
            "I'm not sure I understood that. I can help you with:\n\n- \"Recommend me a job\" — personalized recommendations from your skills.\n- \"How to apply?\" — the application process.\n- \"Cancel my application\" — how to withdraw.\n- \"How many jobs are open?\" — platform statistics.\n\nCould you try rephrasing your question?",
            'text'
        );
    }

    private function recommendJobs(?User $user): array
    {
        $jobs = Job::where('deadline', '>=', today())->get();

        if ($jobs->isEmpty()) {
            return $this->reply('There are currently no open jobs. Please check back later.', 'text');
        }

        if ($user && ($user->skills || $user->job_title)) {
            $scored = $jobs->map(function (Job $job) use ($user) {
                $profileSkills = $user->skillsList();
                $jobSkills = $job->requiredSkillsList();
                $matches = array_intersect($profileSkills, $jobSkills);
                $score = count($matches) * 2;

                if ($user->job_title && str_contains(strtolower($job->title), strtolower($user->job_title))) {
                    $score += 3;
                }

                return ['job' => $job, 'score' => $score, 'matches' => $matches];
            })
                ->filter(fn ($item) => $item['score'] > 0)
                ->sortByDesc('score')
                ->take(5)
                ->values();

            if ($scored->isEmpty()) {
                return $this->reply(
                    "I couldn't find jobs matching your current profile. Try adding more skills to your profile, or browse the Jobs page to see everything available. Here are the latest openings:\n\n" . $this->jobList($jobs->take(3)),
                    'jobs',
                    $jobs->take(3)->pluck('id')->all()
                );
            }

            $lines = "Based on your profile, here are my top recommendations for you:\n\n";
            foreach ($scored as $i => $item) {
                $rank = $i + 1;
                $lines .= "{$rank}. {$item['job']->title} ({$item['job']->category}) - {$item['job']->location}\n";
                if (! empty($item['matches'])) {
                    $lines .= '   Matches your skills: ' . implode(', ', array_slice($item['matches'], 0, 4)) . "\n";
                }
            }
            $lines .= "\nTip: keep your profile skills up to date for better matches.";

            return $this->reply($lines, 'jobs', $scored->pluck('job.id')->all());
        }

        return $this->reply(
            "To give you personalized recommendations, please complete your profile (especially the Skills field) first. Meanwhile, here are the latest open jobs:\n\n" . $this->jobList($jobs->take(5)),
            'jobs',
            $jobs->take(5)->pluck('id')->all()
        );
    }

    private function jobList($jobs): string
    {
        return $jobs->map(
            fn (Job $job) => "• {$job->title} — {$job->location} ({$job->workTypeLabel()})"
        )->implode("\n");
    }

    private function deadlineInfo(?User $user): array
    {
        $open = Job::where('deadline', '>=', today())->count();
        $upcoming = Job::where('deadline', '>=', today())
            ->orderBy('deadline')
            ->take(3)
            ->get();

        $lines = "There are currently {$open} open job(s) accepting applications.\n\n";
        if ($upcoming->isNotEmpty()) {
            $lines .= "Soonest deadlines:\n" . $upcoming->map(
                fn (Job $job) => "• {$job->title} — closes " . $job->deadline->format('d M Y')
            )->implode("\n");
        }

        return $this->reply($lines, 'text');
    }

    private function stats(?User $user): array
    {
        $open = Job::where('deadline', '>=', today())->count();
        $total = Job::count();
        $candidates = User::where('role', 'candidate')->count();
        $applications = \App\Models\Application::count();

        return $this->reply(
            "Platform statistics:\n\n• Open jobs: {$open}\n• Total jobs posted: {$total}\n• Registered candidates: {$candidates}\n• Total applications: {$applications}",
            'text'
        );
    }

    private function jobInfo(?User $user): array
    {
        $sample = Job::where('deadline', '>=', today())->latest()->first();

        if (! $sample) {
            return $this->reply('There are no open jobs at the moment.', 'text');
        }

        return $this->reply(
            "Here's a sample job listing:\n\n{$sample->title}\nCategory: {$sample->category}\nLocation: {$sample->location}\nWork type: {$sample->workTypeLabel()}\nSalary: " . ($sample->salary ? '$' . number_format((float) $sample->salary, 2) : 'Not specified') . "\nDeadline: {$sample->deadline->format('d M Y')}\nRequired skills: {$sample->required_skills}",
            'text'
        );
    }

    private function askAi(string $message, ?User $user): ?string
    {
        if (! config('services.openrouter.api_key') || ! config('services.openrouter.model')) {
            return null;
        }

        try {
            $messages = [
                ['role' => 'system', 'content' => $this->systemPrompt($user)],
                ...$this->historyMessages($user),
                ['role' => 'user', 'content' => $message],
            ];

            return $this->openRouter->chat($messages);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Chatbot AI request failed: ' . $e->getMessage());

            return null;
        }
    }

    private function systemPrompt(?User $user): string
    {
        return "You are Jobot, the AI assistant for the 'AI Job Board' website. Answer ONLY using the data below. Never invent jobs or numbers. Keep answers short. Use bullet points. If the question can't be answered from the data, say so.\n\n"
            . $this->platformContext($user);
    }

    private function platformContext(?User $user): string
    {
        $lines = '';

        $open = Job::where('deadline', '>=', today())->count();
        $total = Job::count();
        $candidates = User::where('role', 'candidate')->count();
        $applications = Application::count();

        $lines .= "Stats: {$open} open jobs, {$total} total, {$candidates} candidates, {$applications} applications\n\n";

        $lines .= "Jobs:\n";
        Job::orderBy('deadline')->take(30)->get()->each(function (Job $job) use (&$lines) {
            $lines .= "- {$job->title} | {$job->category} | {$job->location} | {$job->workTypeLabel()} | "
                . ($job->salary ? '$' . number_format((float) $job->salary) : 'N/A') . " | skills: {$job->required_skills}\n";
        });

        if ($user && $user->isCandidate()) {
            $lines .= "\nUser: {$user->name}, title: " . ($user->job_title ?: 'N/A') . ", skills: " . ($user->skills ?: 'N/A') . ", apps: " . $user->applications()->count() . "\n";
        }

        if ($user && $user->isAdmin()) {
            $top = Job::withCount('applications')->orderByDesc('applications_count')->first();
            $lines .= "\nAdmin: top job = " . ($top ? "{$top->title} ({$top->applications_count} apps)" : 'none') . "\n";
        }

        return $lines;
    }

    private function historyMessages(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return $user->chatMessages()->latest()->take(4)->get()->reverse()
            ->flatMap(fn (ChatMessage $m) => [
                ['role' => 'user', 'content' => $m->message],
                ['role' => 'assistant', 'content' => $m->response],
            ])
            ->all();
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function reply(string $text, string $type, array $jobIds = []): array
    {
        return [
            'text' => $text,
            'type' => $type,
            'job_ids' => $jobIds,
        ];
    }
}
