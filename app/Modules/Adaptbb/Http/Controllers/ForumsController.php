<?php

namespace App\Modules\Adaptbb\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Modules\Adaptbb\Models\Forum;
use App\Modules\Adaptbb\Models\Topic;

use Cache;
use Theme;

class ForumsController extends Controller
{
    /**
    * Index
    *
    * @return View
    */
    public function index()
    {
        $forum = new Forum;
        $forums = $forum->getIndex();

        $this->theme->setTitle('Community Forums');

        return $this->theme->scope('adaptbb.forums.index', compact('forums'))->render();
    }

    /**
    * View
    *
    * @param string $slug
    *
    * @return View
    */
    public function view($slug)
    {
        $forum = Forum::where('slug', '=', $slug)->first();

        if (empty($forum)) {
            abort(404, 'Cannot find forum.');
        }

        $topics = Topic::where('forum_id', '=', $forum->id)->paginate(15);

        $this->theme->set('meta_keywords', $forum->meta_keywords);
        $this->theme->set('meta_description', $forum->meta_description);
        $this->theme->setTitle('Community Forums - ' . $forum->name);

        return $this->theme->scope('adaptbb.forums.view', compact('forum', 'topics'))->render();
    }
}
