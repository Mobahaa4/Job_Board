<form method="POST" action="{{ $action }}">
    @csrf
    @method($method)

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="title" class="form-label">Job Title</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $job?->title) }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $job?->category) }}" placeholder="e.g. IT, Design, Marketing" required>
            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Job Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $job?->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="required_skills" class="form-label">Required Skills</label>
        <textarea class="form-control @error('required_skills') is-invalid @enderror" id="required_skills" name="required_skills" rows="3" placeholder="Separate skills with commas, e.g. PHP, Laravel, MySQL" required>{{ old('required_skills', $job?->required_skills) }}</textarea>
        <div class="form-text">The AI chatbot matches these against candidate profiles.</div>
        @error('required_skills') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $job?->location) }}" placeholder="e.g. Cairo" required>
            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="work_type" class="form-label">Work Type</label>
            <select class="form-select @error('work_type') is-invalid @enderror" id="work_type" name="work_type" required>
                <option value="remote" @selected(old('work_type', $job?->work_type) === 'remote')>Remote</option>
                <option value="on-site" @selected(old('work_type', $job?->work_type) === 'on-site')>On-site</option>
                <option value="hybrid" @selected(old('work_type', $job?->work_type) === 'hybrid')>Hybrid</option>
            </select>
            @error('work_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="salary" class="form-label">Salary (per year)</label>
            <input type="number" step="0.01" min="0" class="form-control @error('salary') is-invalid @enderror" id="salary" name="salary" value="{{ old('salary', $job?->salary) }}" placeholder="e.g. 50000">
            @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-4">
        <label for="deadline" class="form-label">Application Deadline</label>
        <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', $job?->deadline?->format('Y-m-d')) }}" required>
        @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $button }}</button>
    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
