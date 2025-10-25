<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;
    protected $course;
    protected $comment;


    public function __construct(Course $course, Comment $comment)
    {
         $this->course = $course; 
         $this->comment = $comment;
    }

    
    public function via($notifiable)
    {
        return ['database'];
    }

    
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'A new comment has been added to your course: ' . $this->course->title,
            'comment' => $this->comment->content,  
            'student_name' => $this->comment->user->name,
            'course_id' => $this->course->id,
        ];
    }
}
