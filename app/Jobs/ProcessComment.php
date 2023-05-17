<?php

namespace App\Jobs;

use App\Models\NewsComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $commentData;

    /**
     * Create a new job instance.
     *
     * @param  array  $commentData
     * @return void
     */
    public function __construct(array $commentData)
    {
        $this->commentData = $commentData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $comment = new NewsComment();
        $comment->body = $this->commentData['body'];
        $comment->user_id = $this->commentData['user_id'];
        $comment->news_id = $this->commentData['news_id'];
        $comment->save();
    }
}
