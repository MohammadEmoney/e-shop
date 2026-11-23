<?php

namespace App\Livewire\Admin\Blogs;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\Blog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('مقاله')]
class Form extends Component
{
    use DispatchesToasts;
    use WithFileUploads;

    public ?Blog $blog = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $content = '';

    public bool $is_published = true;

    public ?string $published_at = null;

    /** @var UploadedFile|array<int, UploadedFile>|null */
    public $uploads = null;

    public function mount(?Blog $blog = null): void
    {
        $this->authorizeAdmin($blog ? 'blogs.edit' : 'blogs.create');

        $this->blog = $blog;

        if ($blog) {
            $this->title = $blog->title;
            $this->slug = $blog->slug;
            $this->excerpt = $blog->excerpt ?? '';
            $this->content = $blog->content;
            $this->is_published = $blog->is_published;
            $this->published_at = $blog->published_at?->format('Y-m-d');
        }
    }

    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    public function updatedTitle(): void
    {
        if (! $this->blog) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function removeMedia(int $mediaId): void
    {
        $this->authorizeAdmin($this->blog ? 'blogs.edit' : 'blogs.create');

        if ($this->blog) {
            $this->blog->deleteMedia($mediaId);
        }

        $this->flashToast('فایل حذف شد.');
    }

    public function save(): void
    {
        $this->authorizeAdmin($this->blog ? 'blogs.edit' : 'blogs.create');

        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blogs,slug,'.($this->blog->id ?? 'NULL')],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['published_at'] = $this->published_at ? Carbon::parse($this->published_at) : null;

        if ($this->blog) {
            $this->blog->update($data);
            $blog = $this->blog;
        } else {
            $blog = Blog::create($data);
        }

        if ($this->uploads) {
            $files = is_array($this->uploads) ? $this->uploads : [$this->uploads];

            foreach ($files as $file) {
                $source = $this->resolveUploadedSource($file);
                if ($source !== null) {
                    $blog->addMedia($source)->toMediaCollection('images');
                }
            }
        }

        $this->flashToast('مقاله ذخیره شد.');

        $this->redirectRoute('admin.blogs', navigate: true);
    }

    /**
     * Resolve a path (or UploadedFile) acceptable by Spatie media's addMedia(),
     * regardless of whether the source is a Livewire TemporaryUploadedFile or a
     * regular Illuminate UploadedFile.
     *
     * @return UploadedFile|string|null
     */
    protected function resolveUploadedSource(UploadedFile|string $file)
    {
        if (is_string($file)) {
            return $file;
        }

        return $file->getRealPath();
    }

    public function render(): string
    {
        return 'livewire.admin.blogs.form';
    }
}
