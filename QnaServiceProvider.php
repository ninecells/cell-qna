<?php

namespace ModernPUG\Qna;

use App;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\ServiceProvider;
use Mews\Purifier\PurifierServiceProvider;
use NineCells\Assets\Twbs3\Twbs3JumboNarrowServiceProvider;
use NineCells\Admin\AdminServiceProvider;
use NineCells\Auth\AuthServiceProvider;
use NineCells\Auth\MemberTab;
use NineCells\Admin\PackageList;

use ModernPUG\Qna\Models\Question;
use ModernPUG\Qna\Models\Answer;
use ModernPUG\Qna\Models\Comment;
use ModernPUG\Qna\Models\Vote;
use ModernPUG\Qna\Policies\QnaPolicy;

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

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'mpug');

        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('vendor/modern-pug/qna'),
        ], 'public');

        $tab->addMemberTabItemInfo('qna', 'Q&A', function($member_id) {
            return route('mpug::url.qna.member_qna', $member_id);
        });

        $packages->addPackageInfo('qna', 'Q&A', function() {
            return 'QnaServiceProvider.php를 수정하세요';
        });
    }

    public function register()
    {
        App::register(AuthServiceProvider::class);
        App::register(AdminServiceProvider::class);
        App::register(Twbs3JumboNarrowServiceProvider::class);
        App::register(PurifierServiceProvider::class);
    }
}
