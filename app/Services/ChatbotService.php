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

        return $this->fallbackReply($text, $user);
    }

    private function fallbackReply(string $text, ?User $user): array
    {
        if ($user && $user->isAdmin()) {
            return $this->adminFallback($text);
        }

        if ($user && $user->isCandidate()) {
            return $this->candidateFallback($text, $user);
        }

        return $this->guestFallback($text);
    }

    private function adminFallback(string $text): array
    {
        if ($this->containsAny($text, ['count', 'how many', 'number of', 'statistics', 'stats', 'total'])) {
            $open = Job::where('deadline', '>=', today())->count();
            $total = Job::count();
            $candidates = User::where('role', 'candidate')->count();
            $applications = Application::count();
            return $this->reply(
                "Platform statistics:\n\n• Open jobs: {$open}\n• Total jobs posted: {$total}\n• Registered candidates: {$candidates}\n• Total applications: {$applications}",
                'text'
            );
        }

        if ($this->containsAny($text, ['most applications', 'popular job', 'most applied', 'highest applications'])) {
            $top = Job::withCount('applications')->orderByDesc('applications_count')->first();
            if ($top && $top->applications_count > 0) {
                return $this->reply(
                    "\"{$top->title}\" has the most applications with {$top->applications_count} application(s).\n\nCategory: {$top->category}\nLocation: {$top->location}\nWork type: {$top->workTypeLabel()}",
                    'text'
                );
            }
            return $this->reply("No jobs have received applications yet.", 'text');
        }

        if ($this->containsAny($text, ['category'])) {
            $cat = null;
            foreach (['IT', 'Data Science', 'Design', 'Marketing', 'HR', 'Finance', 'Support', 'Management', 'Sales', 'Logistics', 'Legal', 'Education'] as $c) {
                if (str_contains($text, strtolower($c))) {
                    $cat = $c;
                    break;
                }
            }
            if ($cat) {
                $jobs = Job::where('category', $cat)->where('deadline', '>=', today())->get();
                if ($jobs->isEmpty()) {
                    return $this->reply("There are no open {$cat} jobs at the moment.", 'text');
                }
                $lines = "Open {$cat} jobs ({$jobs->count()}):\n\n";
                foreach ($jobs as $job) {
                    $lines .= "• {$job->title} — {$job->location} ({$job->workTypeLabel()}) | " . ($job->salary ? '$' . number_format((float) $job->salary) : 'N/A') . "\n";
                }
                return $this->reply($lines, 'jobs', $jobs->pluck('id')->all());
            }
            $cats = Job::where('deadline', '>=', today())->selectRaw('category, count(*) as cnt')->groupBy('category')->orderByDesc('cnt')->get();
            $lines = "Jobs by category:\n\n";
            foreach ($cats as $c) {
                $lines .= "• {$c->category}: {$c->cnt} open job(s)\n";
            }
            return $this->reply($lines, 'text');
        }

        if ($this->containsAny($text, ['list candidates', 'all candidates', 'show candidates', 'show all candidates', 'list all candidates'])) {
            $candidates = User::where('role', 'candidate')->orderBy('name')->get();
            if ($candidates->isEmpty()) {
                return $this->reply("No candidates registered yet.", 'text');
            }
            $lines = "All registered candidates ({$candidates->count()}):\n\n";
            foreach ($candidates as $c) {
                $lines .= "• {$c->name} — {$c->email}\n  Title: " . ($c->job_title ?: 'N/A') . " | Skills: " . ($c->skills ?: 'N/A') . "\n";
            }
            return $this->reply($lines, 'text');
        }

        if ($this->containsAny($text, ['show applications', 'all applications', 'list applications', 'list all applications', 'show all applications'])) {
            $apps = Application::with(['user', 'job'])->orderByDesc('created_at')->get();
            if ($apps->isEmpty()) {
                return $this->reply("No applications have been submitted yet.", 'text');
            }
            $lines = "All applications ({$apps->count()}):\n\n";
            foreach ($apps as $a) {
                $lines .= "• {$a->user->name} → {$a->job->title} | Status: {$a->statusLabel()} | {$a->created_at->format('d M Y')}\n";
            }
            return $this->reply($lines, 'text');
        }

        if ($this->containsAny($text, ['add job', 'add a job', 'create job', 'create a job', 'new job', 'post job'])) {
            return $this->reply(
                "To add a new job:\n\n1. Go to Admin Panel > Add Job.\n2. Fill in the title, description, required skills, category, location, work type, salary and deadline.\n3. Click Save.",
                'text'
            );
        }

        if ($this->containsAny($text, ['edit job', 'edit a job', 'update job', 'modify job'])) {
            return $this->reply(
                "To edit a job:\n\n1. Go to Admin Panel > Manage Jobs.\n2. Click Edit on the job you want to modify.\n3. Update the fields and click Save.",
                'text'
            );
        }

        if ($this->containsAny($text, ['delete job', 'delete a job', 'remove job', 'remove a job'])) {
            return $this->reply(
                "To delete a job:\n\n1. Go to Admin Panel > Manage Jobs.\n2. Click Delete on the job you want to remove.\n3. Confirm the deletion.",
                'text'
            );
        }

        if ($this->containsAny($text, ['list jobs', 'all jobs', 'list all jobs', 'show jobs', 'show all jobs'])) {
            $jobs = Job::orderBy('deadline')->get();
            $lines = "All jobs ({$jobs->count()}):\n\n";
            foreach ($jobs as $job) {
                $lines .= "• {$job->title} — {$job->category} | {$job->location} ({$job->workTypeLabel()}) | " . ($job->salary ? '$' . number_format((float) $job->salary) : 'N/A') . "\n";
            }
            return $this->reply($lines, 'jobs', $jobs->pluck('id')->all());
        }

        if ($this->containsAny($text, ['skills'])) {
            $skills = User::where('role', 'candidate')->whereNotNull('skills')->pluck('skills');
            $allSkills = [];
            foreach ($skills as $s) {
                foreach (array_map('trim', explode(',', $s)) as $skill) {
                    if ($skill !== '') {
                        $allSkills[strtolower($skill)] = ($allSkills[strtolower($skill)] ?? 0) + 1;
                    }
                }
            }
            arsort($allSkills);
            $top = array_slice($allSkills, 0, 10, true);
            $lines = "Most common candidate skills:\n\n";
            foreach ($top as $skill => $count) {
                $lines .= "• " . ucfirst($skill) . ": {$count} candidate(s)\n";
            }
            return $this->reply($lines, 'text');
        }

        if ($this->containsAny($text, ['hello', 'hi', 'hey'])) {
            return $this->reply("Hello! I'm Jobot, your admin assistant. I can show you platform statistics, candidate lists, application statuses, and job data. What would you like to know?", 'text');
        }

        if ($this->containsAny($text, ['thank', 'thanks'])) {
            return $this->reply("You're welcome! Let me know if you need anything else about the platform.", 'text');
        }

        return $this->reply(
            "I can answer any question about the platform! Try asking:\n\n- \"Which job has the most applications?\"\n- \"Show jobs in the IT category\"\n- \"What skills do most candidates have?\"\n- \"List all candidates\"\n- \"How many applications are there?\"\n- \"How do I add a new job?\"",
            'text'
        );
    }

    private function candidateFallback(string $text, User $user): array
    {
        if ($this->containsAny($text, ['recommend', 'suggest', 'suitable', 'best job', 'jobs for me', 'fit', 'match', 'find a job', 'find job', 'find me'])) {
            return $this->recommendJobs($user);
        }

        if ($this->containsAny($text, ['count', 'how many', 'number of', 'statistics', 'stats', 'total'])) {
            $open = Job::where('deadline', '>=', today())->count();
            $total = Job::count();
            $applications = $user->applications()->count();
            return $this->reply(
                "Platform statistics:\n\n• Open jobs: {$open}\n• Total jobs posted: {$total}\n• Your applications: {$applications}",
                'text'
            );
        }

        if ($this->containsAny($text, ['skills should i learn', 'what skills', 'skills to learn', 'learn skills', 'improve skills'])) {
            $userSkills = $user->skillsList();
            $jobSkills = [];
            Job::where('deadline', '>=', today())->get()->each(function (Job $job) use (&$jobSkills) {
                foreach ($job->requiredSkillsList() as $s) {
                    $jobSkills[strtolower($s)] = ($jobSkills[strtolower($s)] ?? 0) + 1;
                }
            });
            arsort($jobSkills);
            $missing = array_diff_key($jobSkills, array_flip(array_map('strtolower', $userSkills)));
            $top = array_slice($missing, 0, 5, true);
            if (empty($top)) {
                return $this->reply("Your skills already cover most in-demand skills! Keep them updated.", 'text');
            }
            $lines = "Based on current job listings, these skills are most in-demand but missing from your profile:\n\n";
            foreach ($top as $skill => $count) {
                $lines .= "• " . ucfirst($skill) . " — required in {$count} open job(s)\n";
            }
            $lines .= "\nAdding these to your profile will improve your job recommendations.";
            return $this->reply($lines, 'text');
        }

        if ($this->containsAny($text, ['remote', 'hybrid', 'on-site', 'onsite'])) {
            $workType = $this->containsAny($text, ['remote']) ? 'remote' : ($this->containsAny($text, ['hybrid']) ? 'hybrid' : 'on-site');
            $jobs = Job::where('work_type', $workType)->where('deadline', '>=', today())->take(10)->get();
            if ($jobs->isEmpty()) {
                return $this->reply("There are currently no {$workType} jobs available. Try checking back later.", 'text');
            }
            $lines = "Here are the {$workType} jobs:\n\n";
            foreach ($jobs as $job) {
                $lines .= "• {$job->title} — {$job->category} | {$job->location}";
                if ($job->salary) {
                    $lines .= " | $" . number_format((float) $job->salary);
                }
                $lines .= "\n";
            }
            return $this->reply($lines, 'jobs', $jobs->pluck('id')->all());
        }

        if ($this->containsAny($text, ['cancel', 'withdraw', 'remove application'])) {
            return $this->reply(
                "To cancel an application:\n\n1. Go to 'My Applications'.\n2. Find the job you applied to.\n3. Click 'Cancel Application'.",
                'text'
            );
        }

        if ($this->containsAny($text, ['edit profile', 'update profile', 'profile', 'change my info', 'resume', 'cv', 'upload'])) {
            return $this->reply(
                "You can update your profile from the 'My Profile' page. There you can edit your full name, age, job title, description, phone, skills, profile image and resume (CV).",
                'text'
            );
        }

        if ($this->containsAny($text, ['hello', 'hi', 'hey'])) {
            return $this->reply("Hello! I'm Jobot, your job assistant. I can recommend jobs based on your skills, show you available openings, and help you navigate the platform. What would you like to know?", 'text');
        }

        if ($this->containsAny($text, ['thank', 'thanks'])) {
            return $this->reply("You're welcome! Good luck with your job search. Ask me anytime if you need help.", 'text');
        }

        if ($this->containsAny($text, ['bye', 'goodbye'])) {
            return $this->reply("Goodbye! I hope you find your dream job. Come back anytime.", 'text');
        }

        return $this->reply(
            "I can answer any question about the platform! Try asking:\n\n- \"Best jobs for me\" — personalized recommendations.\n- \"What skills should I learn?\" — in-demand skills for your profile.\n- \"Show me remote jobs\" — filter by work type.\n- \"How many jobs are open?\" — current stats.\n- \"Edit my profile\" — update your skills, resume, etc.",
            'text'
        );
    }

    private function guestFallback(string $text): array
    {
        if ($this->containsAny($text, ['hello', 'hi', 'hey'])) {
            return $this->reply("Hello! I'm Jobot. I can help you explore available jobs on AI Job Board. Try asking: \"What jobs are available?\" or \"Show me remote jobs\".", 'text');
        }

        if ($this->containsAny($text, ['thank', 'thanks'])) {
            return $this->reply("You're welcome! Register an account to apply for jobs and get personalized recommendations.", 'text');
        }

        if ($this->containsAny($text, ['bye', 'goodbye'])) {
            return $this->reply("Goodbye! Come back anytime to explore available jobs.", 'text');
        }

        if ($this->containsAny($text, ['count', 'how many', 'number of', 'statistics', 'stats', 'total'])) {
            $open = Job::where('deadline', '>=', today())->count();
            $total = Job::count();
            return $this->reply(
                "Platform statistics:\n\n• Open jobs: {$open}\n• Total jobs posted: {$total}",
                'text'
            );
        }

        if ($this->containsAny($text, ['remote', 'hybrid', 'on-site', 'onsite'])) {
            $workType = $this->containsAny($text, ['remote']) ? 'remote' : ($this->containsAny($text, ['hybrid']) ? 'hybrid' : 'on-site');
            $jobs = Job::where('work_type', $workType)->where('deadline', '>=', today())->take(10)->get();
            if ($jobs->isEmpty()) {
                return $this->reply("There are currently no {$workType} jobs available. Try checking back later.", 'text');
            }
            $lines = "Here are the {$workType} jobs:\n\n";
            foreach ($jobs as $job) {
                $lines .= "• {$job->title} — {$job->category} | {$job->location}";
                if ($job->salary) {
                    $lines .= " | $" . number_format((float) $job->salary);
                }
                $lines .= "\n";
            }
            return $this->reply($lines, 'jobs', $jobs->pluck('id')->all());
        }

        return $this->reply(
            "I can help you with:\n\n- \"What are the best jobs?\" — list available jobs.\n- \"How many jobs are open?\" — current stats.\n- \"Show me remote jobs\" — filter by work type.",
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

            return $this->openRouter->chat($messages, 800);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Chatbot AI failed after all retries: ' . $e->getMessage());

            return null;
        }
    }

    private function systemPrompt(?User $user): string
    {
        $role = $user?->isAdmin() ? 'admin' : ($user?->isCandidate() ? 'candidate' : 'guest');

        $roleInstructions = match ($role) {
            'admin' => "You are helping an ADMIN of this website. The admin has FULL access to everything:
- View, add, edit, and delete any job listing
- View ALL candidates (names, emails, skills, job titles, resumes)
- View ALL applications with statuses (pending, accepted, rejected)
- See platform-wide statistics (total jobs, candidates, applications, open positions)
- The admin can also answer how to perform admin actions (add/edit/delete jobs)

You MUST answer any question the admin asks about the platform. Use the database data provided below to give accurate, specific answers. You can list candidates by name, list applications, show job application counts, compare jobs, analyze data, and answer any analytical question.",

            'candidate' => "You are helping a CANDIDATE (regular user). They can:
- Browse and search for available jobs
- Apply for jobs and manage their own applications
- Update their own profile (skills, resume, job title)
- Get personalized job recommendations based on their profile skills

IMPORTANT PRIVACY RULES for candidates:
- NEVER reveal other candidates' names, emails, skills, or any personal data
- NEVER reveal application counts per job or admin-level statistics
- NEVER mention other users' applications or statuses
- ONLY show the candidate's OWN applications and profile data
- If asked about other candidates or admin data, politely say you can't share that information",

            'guest' => "You are helping a GUEST (not logged in). They can only:
- Browse available job listings
- See job titles, categories, locations, salaries, required skills, and work types
- They cannot apply for jobs or see any user data

If they want to apply, suggest they register an account.",
        };

        return "You are Jobot, the AI assistant for the 'AI Job Board' website — a job platform where employers post jobs and candidates apply.

YOUR CAPABILITIES:
- You can answer ANY question about this platform. Don't limit yourself to specific examples.
- Use the database data provided below to give accurate, specific, and detailed answers.
- You can analyze data, compare jobs, find patterns, make recommendations, and explain anything about the platform.
- You can answer questions about job categories, salary ranges, locations, required skills, work types, application processes, and more.
- You can answer general knowledge questions related to jobs, careers, and the job market if they relate to the platform data.

ANSWER STYLE:
- Be helpful, conversational, and thorough.
- Use bullet points and short paragraphs for readability.
- When listing jobs, include: title, category, location, work type, salary, and required skills.
- When the user asks for recommendations, explain WHY you're recommending something.
- If you don't have enough data to answer, say so honestly.

{$roleInstructions}

" . $this->platformContext($user);
    }

    private function platformContext(?User $user): string
    {
        $role = $user?->isAdmin() ? 'admin' : ($user?->isCandidate() ? 'candidate' : 'guest');
        $lines = "=== DATABASE DATA (use this to answer questions) ===\n\n";

        // --- Stats (all roles see basic stats) ---
        $open = Job::where('deadline', '>=', today())->count();
        $total = Job::count();
        $lines .= "Summary: {$open} open jobs, {$total} total jobs posted\n";

        if ($role === 'admin') {
            $candidates = User::where('role', 'candidate')->count();
            $applications = Application::count();
            $lines .= "Admin stats: {$candidates} registered candidates, {$applications} total applications\n";
        }

        // --- Categories breakdown ---
        $categories = Job::where('deadline', '>=', today())
            ->selectRaw('category, count(*) as cnt')
            ->groupBy('category')
            ->orderByDesc('cnt')
            ->get();
        $lines .= "\nCategories: " . $categories->map(fn ($c) => "{$c->category} ({$c->cnt})")->implode(', ') . "\n";

        // --- Work type breakdown ---
        $workTypes = Job::where('deadline', '>=', today())
            ->selectRaw('work_type, count(*) as cnt')
            ->groupBy('work_type')
            ->get();
        $lines .= "Work types: " . $workTypes->map(fn ($w) => "{$w->work_type} ({$w->cnt})")->implode(', ') . "\n";

        // --- Location breakdown ---
        $locations = Job::where('deadline', '>=', today())
            ->selectRaw('location, count(*) as cnt')
            ->groupBy('location')
            ->orderByDesc('cnt')
            ->get();
        $lines .= "Locations: " . $locations->map(fn ($l) => "{$l->location} ({$l->cnt})")->implode(', ') . "\n";

        // --- Salary range ---
        $salaryStats = Job::where('deadline', '>=', today())->whereNotNull('salary')
            ->selectRaw('min(salary) as min_sal, max(salary) as max_sal, avg(salary) as avg_sal')
            ->first();
        if ($salaryStats && $salaryStats->min_sal) {
            $lines .= "Salary range: $" . number_format($salaryStats->min_sal) . " - $" . number_format($salaryStats->max_sal) . " (avg: $" . number_format(round($salaryStats->avg_sal)) . ")\n";
        }

        // --- All jobs (open first, then closed) ---
        $openJobs = Job::where('deadline', '>=', today())->orderBy('deadline')->take(15)->get();
        $closedJobs = Job::where('deadline', '<', today())->orderByDesc('deadline')->take(5)->get();
        $allJobs = $openJobs->merge($closedJobs);
        $lines .= "\nJobs:\n";
        $allJobs->each(function (Job $job) use (&$lines) {
            $status = $job->deadline->isPast() ? ' [CLOSED]' : '';
            $lines .= "- {$job->title} | {$job->category} | {$job->location} | {$job->workTypeLabel()} | "
                . ($job->salary ? '$' . number_format((float) $job->salary) : 'N/A')
                . " | skills: {$job->required_skills}{$status}\n";
        });

        // --- Candidate: own profile + own applications ---
        if ($role === 'candidate') {
            $lines .= "\nYour profile:\n";
            $lines .= "- Name: {$user->name}\n";
            $lines .= "- Job title: " . ($user->job_title ?: 'Not set') . "\n";
            $lines .= "- Skills: " . ($user->skills ?: 'Not set') . "\n";
            $lines .= "- Age: " . ($user->age ?: 'Not set') . "\n";
            $lines .= "- Phone: " . ($user->phone ?: 'Not set') . "\n";

            $apps = $user->applications()->with('job')->get();
            if ($apps->isNotEmpty()) {
                $lines .= "\nYour applications ({$apps->count()}):\n";
                foreach ($apps as $app) {
                    $lines .= "- {$app->job->title} ({$app->statusLabel()}) — applied {$app->created_at->format('d M Y')}\n";
                }
            } else {
                $lines .= "\nYour applications: none yet\n";
            }
        }

        // --- Admin: summary + top candidates + top applications ---
        if ($role === 'admin') {
            $candidateCount = User::where('role', 'candidate')->count();
            $appCount = Application::count();
            $lines .= "\nAdmin: {$candidateCount} total candidates, {$appCount} total applications\n";

            $lines .= "\nTop candidates:\n";
            User::where('role', 'candidate')->orderBy('name')->take(10)->get()->each(function (User $u) use (&$lines) {
                $lines .= "- {$u->name} | {$u->email} | " . ($u->job_title ?: 'N/A') . " | " . ($u->skills ?: 'N/A') . "\n";
            });

            if ($appCount > 0) {
                $lines .= "\nRecent applications:\n";
                Application::with(['user', 'job'])->orderByDesc('created_at')->take(10)->get()->each(function (Application $a) use (&$lines) {
                    $lines .= "- {$a->user->name} → {$a->job->title} | {$a->statusLabel()}\n";
                });
            }

            $lines .= "\nApplications per job:\n";
            Job::where('deadline', '>=', today())->withCount('applications')->orderByDesc('applications_count')->take(10)->get()->each(function (Job $j) use (&$lines) {
                if ($j->applications_count > 0) {
                    $lines .= "- {$j->title}: {$j->applications_count}\n";
                }
            });
        }

        $lines .= "\n=== END DATABASE DATA ===";

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
