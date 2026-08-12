<?php

namespace App\Livewire\Pages;

use App\Mail\ContactSubmissionMail;
use App\Mail\ContactConfirmationMail;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Contac Us')]
class Contact extends Component {
    public string $name = '';
    public string $email = '';
    public string $company = '';
    public string $subject = '';
    public $message = '';

    public $sample = '';

    // Honeypot
    public string $website = '';

    public bool $submitted = false;

    public function getMessageLengthProperty(): int {
        return strlen($this->message);
    }

    protected function rules(): array {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],

            'company' => [
                'nullable',
                'string',
                'max:150',
            ],

            'subject' => [
                'required',
                'string',
                'min:3',
                'max:150',
            ],

            'message' => [
                'required',
                'string',
                'min:20',
                'max:5000',
            ],

            'website' => [
                'nullable',
                'max:0',
            ],
        ];
    }

    protected array $messages = [
        'name.required' => 'Please tell us your name.',
        'email.required' => 'Please provide your email address.',
        'email.email' => 'Please enter a valid email address.',
        'subject.required' => 'Please enter a subject.',
        'message.required' => 'Please tell us how we can help.',
        'message.min' => 'Please provide a little more detail.',
    ];

    public function updated($property): void {
        $this->validateOnly($property);
    }

    public function submit(): void {
        $this->submitted = false;

        $key = 'contact-form:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError(
                'form',
                'Too many submissions. Please try again in ' . $seconds . ' seconds.'
            );

            return;
        }

        RateLimiter::hit($key, 600);

        $this->validate();

        // Using this to prevent spam bot.
        if (filled($this->website)) {
            $this->submitted = true;
            return;
        }

        $submission = ContactSubmission::create([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'company' => filled($this->company)
                ? trim($this->company)
                : null,
            'subject' => trim($this->subject),
            'message' => trim($this->message),
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit(
                request()->userAgent() ?? '',
                1000
            ),
        ]);

        $contactEmail = config(
            'mail.contact_to',
            config('mail.from.address')
        );

        if ($contactEmail) {
            Mail::to($contactEmail)
                ->send(new ContactSubmissionMail($submission));
        }

        Mail::to($submission->email)
            ->send(new ContactConfirmationMail($submission));

        $this->reset([
            'name',
            'email',
            'company',
            'subject',
            'message',
            'website',
        ]);

        $this->submitted = true;
    }

    public function render() {
        return view('livewire.pages.contact');
    }
}
