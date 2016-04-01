<?php

namespace NineCells\Qna;

use App;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\ServiceProvider;
use Mews\Purifier\PurifierServiceProvider;
use NineCells\Admin\AdminServiceProvider;
use NineCells\Admin\PackageList;
use NineCells\Assets\Twbs3\Twbs3JumboNarrowServiceProvider;
use NineCells\Member\MemberServiceProvider;
use NineCells\Member\MemberTab;
use NineCells\Qna\Models\Answer;
use NineCells\Qna\Models\Comment;
use NineCells\Qna\Models\Question;
use NineCells\Qna\Models\Vote;
use NineCells\Qna\Policies\QnaPolicy;

class QnaServiceProvider extends ServiceProvider
{
    private $policies = [
        Question::class => QnaPolicy::class,
        Answer::class => QnaPolicy::class,
        Comment::class => QnaPolicy::class,
        Vote::class => QnaPolicy::class,
    ];

    private function registerPolicies(GateContract $gate)
    {
        $gate->before(function ($user, $ability) {
            if ($ability === "qna-write") {
                return $user;
            }
        });

        foreach ($this->policies as $key => $value) {
            $gate->policy($key, $value);
        }
    }

    public function boot(GateContract $gate, MemberTab $tab, PackageList $packages)
    {
        $this->registerPolicies($gate);

        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/Http/routes.php';
        }

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'ncells');

        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ], 'migrations');

        $tab->addMemberTabItemInfo('qna', 'Q&A', function($member_id) {
            return route('ncells::url.qna.member_qna', $member_id);
        });

        $packages->addPackageInfo('qna', 'Q&A', function() {
            return '/admin/qna/trashes';
        });
    }

    public function register()
    {
        App::register(MemberServiceProvider::class);
        App::register(AdminServiceProvider::class);
        App::register(Twbs3JumboNarrowServiceProvider::class);
        App::register(PurifierServiceProvider::class);
    }
}
