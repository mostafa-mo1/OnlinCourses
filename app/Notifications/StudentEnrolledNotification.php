<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentEnrolledNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $course;
    public function __construct(Course $course)
    {
       $this->course = $course; 
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [ 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('A new student enrolled in your course:'. $this->course->title)
    //                 ->action('course_id', url('/'))
    //                 ->line('Thank you for using our application!');
    // }
    public function toDatabase($notifiable)
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'message' => 'A new student has enrolled in your course.'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'message' => 'A new student has enrolled in your course.'
        ];
    }
}
