@component('mail::message')
# ⚠️ PM Alert: Task Overdue

Hi **{{ $task->project?->manager?->name ?? 'Project Manager' }}**,

A task in your project **"{{ $task->project?->name }}"** is now **overdue** and requires your attention.

@component('mail::panel')
**Task:** {{ $task->name }}
**Assigned To:** {{ $task->assignee?->name ?? 'Unassigned' }}
**Priority:** {{ $task->priority->label() }}
**Deadline Was:** {{ $task->deadline?->format('D, d M Y h:i A') }}
**Current Status:** {{ $task->status->label() }}
@endcomponent

Please follow up with the assigned employee and take appropriate action.

@component('mail::button', ['url' => config('app.url'), 'color' => 'red'])
View Project
@endcomponent

Thanks,
**{{ config('app.name') }}** Team
@endcomponent
