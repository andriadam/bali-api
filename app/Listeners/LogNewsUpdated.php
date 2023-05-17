<?php

namespace App\Listeners;

use App\Events\NewsUpdated;
use App\Models\NewsLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogNewsUpdated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\NewsUpdated  $event
     * @return void
     */
    public function handle(NewsUpdated $event)
    {
        $news = $event->news;

        Log::info('Berita telah diupdate: '.$news->title);

        $log = new NewsLog();
        $log->action = 'updated';
        $log->news_id = $news->id;
        $log->user_id = $news->user_id;
        $log->title = $news->title;
        $log->category = $news->category;
        $log->body = $news->body;
        $log->image = $news->image;
        $log->save();
    }
}
