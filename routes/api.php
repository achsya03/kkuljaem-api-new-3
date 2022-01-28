<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\User\UserController;

use App\Http\Controllers\Auth;
use Yajra\Datatables\Datatables;

use App\Models;
use App\Http\Controllers\Banner;
use App\Http\Controllers\Post;
use App\Http\Controllers\User;
use App\Http\Controllers\Packet;
use App\Http\Controllers\Reference;
use App\Http\Controllers\KkuljaemInfo;
use App\Http\Controllers\Testimoni;
use App\Http\Controllers\Notification;
use App\Http\Controllers\BadWord;

use App\Http\Controllers\Classes;
use App\Http\Controllers\TestController;

use App\Http\Controllers\Helper;
use App\Http\Controllers\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'api/auth'], function () {
    Route::post('register',         Auth\RegisterController::class);
    Route::post('login',            Auth\LoginController::class);
    Route::post('logout',           Auth\LogoutController::class);
    #Route::get('send-mail','MailController@sendEmail');
    Route::post('forget-password',   Auth\ForgotPasswordController::class);
    Route::post('change-password',   Auth\ChangePasswordController::class);
    Route::get('verify-mail',       Auth\VerifyEmailController::class);
});

#==========================Student================================

// Route::group(['prefix' => 'api/banner'], function () {
//     Route::get('/',         [Banner\BannerController::class,'allData']);
//     Route::get('/detail',   [Banner\BannerController::class,'detailData']);
// });

// Route::group(['prefix' => 'api/class'], function () {
//     Route::get('/testimoni', [Classes\ClassController::class,'addData']);
// });

// Route::group(['prefix' => 'api/content'], function () {
//     Route::get('/',     [Banner\VideoController::class,'allDataByDate']);
// });

//test
Route::group(['prefix' => 'api/home'], function () {
    Route::get('/',         [Helper\ShowController::class,'home']);
    Route::get('/web',         [Helper\StudentWebController::class,'homeWeb']);
    Route::get('/banner',   [Helper\ShowController::class,'banner']);
    Route::get('/word',     [Helper\ShowController::class,'word']);
    Route::get('/video',    [Helper\ShowController::class,'video']);
    Route::get('/search',   [Helper\ShowController::class,'search']);
});

Route::post('/spaces-upload',                     [Helper\FileController::class,'saveFile']);

Route::group(['prefix' => 'api/classroom'], function () {
    Route::get('/',                     [Helper\ShowController::class,'classroom']);

    //Route::post('/student',             [Classes\StudentController::class,'studentAdd']);
    Route::post('/student-video',       [Classes\StudentController::class,'studentVideoAdd']);
    Route::post('/student-quiz',        [Classes\StudentController::class,'studentQuizAdd']);
    //Route::post('/student-quiz/answer', [Classes\StudentController::class,'studentQuizAdd']);

    Route::get('/category',             [Helper\ShowController::class,'classroomByCategory']);
    Route::get('/detail',               [Helper\ShowController::class,'classroomDetail']);
    Route::get('/registered',           [Helper\ShowController::class,'classroomRegistered']);
    Route::get('/mentor',               [Helper\ShowController::class,'classroomMentorDetail']);
    Route::get('/detail/video',         [Helper\ShowController::class,'classroomVideoDetail']);
    Route::get('/detail/quiz',          [Helper\ShowController::class,'classroomQuizDetail']);
    Route::get('/detail/more',          [Helper\ShowController::class,'classroomVideoMore']);
    Route::get('/detail/task',          [Helper\ShowController::class,'classroomVideoTask']);
    Route::get('/detail/shadowing',     [Helper\ShowController::class,'classroomVideoShadowing']);
    #Route::get('/testimoni', [Helper\ShowController::class,'testimoni']);
});


Route::group(['prefix' => 'api/forum'], function () {
    Route::get('/',                 [Helper\ShowController::class,'forum']);

    Route::post('/post',            [Post\PostController::class,'addForumPost']);##
    Route::delete('/post',          [Post\PostController::class,'deletePost']);#
    Route::post('/comment',         [Post\PostController::class,'addComment']);##
    Route::delete('/comment',       [Post\PostController::class,'deleteComment']);
    Route::post('/post/alert',      [Post\PostController::class,'alertPost']);#
    Route::delete('/post/alert',      [Post\PostController::class,'alertPostDelete']);
    Route::post('/comment/alert',   [Post\PostController::class,'alertComment']);#
    Route::delete('/comment/alert',   [Post\PostController::class,'alertCommentDelete']);#
    Route::post('/like',            [Post\PostController::class,'addLike']);
    Route::delete('/like',          [Post\PostController::class,'deleteLike']);

    Route::get('/detail',           [Helper\ShowController::class,'forumDetail']);
    Route::get('/popular',          [Helper\ShowController::class,'forumByThemePop']);
    Route::get('/latest',           [Helper\ShowController::class,'forumByThemeNew']);
    Route::get('/posting',          [Helper\ShowController::class,'forumByUser']);
});

Route::group(['prefix' => 'api/qna'], function () {
    Route::get('/',                 [Helper\ShowController::class,'qna']);

    Route::get('/theme',            [Post\PostController::class,'qnaTheme']);
    Route::post('/post',            [Post\PostController::class,'addQnAPost']);##
    Route::delete('/post',          [Post\PostController::class,'deletePost']);#
    Route::post('/comment',         [Post\PostController::class,'addComment']);##
    Route::delete('/comment',       [Post\PostController::class,'deleteComment']);#
    Route::post('/post/alert',      [Post\PostController::class,'alertPost']);#
    Route::delete('/post/alert',      [Post\PostController::class,'alertPostDelete']);
    Route::post('/comment/alert',   [Post\PostController::class,'alertComment']);#
    Route::delete('/comment/alert',   [Post\PostController::class,'alertCommentDelete']);
    Route::post('/like',            [Post\PostController::class,'addLike']);
    Route::delete('/like',          [Post\PostController::class,'deleteLike']);

    Route::get('/video',            [Helper\ShowController::class,'qnaByVideo']);
    Route::get('/posting',          [Helper\ShowController::class,'qnaByUser']);
    Route::get('/detail',           [Helper\ShowController::class,'qnaDetail']);
});

Route::group(['prefix' => 'api/user'], function () {
    Route::get('detail',      [User\UserController::class,'detailUserData']);
    Route::post('update',   [User\UserController::class, 'updateDataStudent']);
    Route::post('device-id',   [User\UserController::class, 'updateDeviceID']);
    Route::post('change-password',      [Auth\ChangePasswordLoginController::class,'changePassword']);
    // Route::get('/',         [User\UserController::class, 'allData']);
    // Route::post('/',        [User\UserController::class, 'addData']);
});
Route::group(['prefix' => 'api/force'], function () {
    Route::post('subs',      [Helper\ForceController::class,'forceSubs']);
    // Route::get('words/url',      [Helper\ForceController::class,'forceWordUrl']);
    // Route::get('words/path',      [Helper\ForceController::class,'forceWordPath']);
    // Route::get('question/path',      [Helper\ForceController::class,'forceQuestionPath']);
    // Route::get('post/url',      [Helper\ForceController::class,'forcePostUrl']);
    // Route::get('banner/url',      [Helper\ForceController::class,'forceBannerUrl']);
    // Route::get('question/url',      [Helper\ForceController::class,'forceQuestionUrl']);
    // Route::get('option/url',      [Helper\ForceController::class,'forceOptionUrl']);
    // Route::get('mentor-detail/url',      [Helper\ForceController::class,'forceMentorDetailUrl']);
    // Route::get('classes/url',      [Helper\ForceController::class,'forceClassesUrl']);
});    
Route::group(['prefix' => 'api/user/packet'], function () {
    Route::get('/',         [Packet\PacketController::class,'allData']);
    //Route::get('/detail',         [Packet\PacketController::class,'detailSelect']);
});
Route::group(['prefix' => 'api/user/subs'], function () {
    Route::get('/ios', [Payment\SubsController::class,'checkIosData']);
    Route::post('/ios', [Payment\SubsController::class,'addIosData']);
    Route::get('/', [Payment\SubsController::class,'detailByUser']);
    Route::get('/detail', [Payment\SubsController::class,'detailSubs']);
    Route::post('/', [Payment\SubsController::class,'doCheckout']);
});
// Route::group(['prefix' => 'videos/redirect'], function () {
//     Route::get('/', [Helper\RedirectVideoController::class,'getVideos']);
// });
// Route::group(['prefix' => 'video/redirect'], function () {
//     Route::get('/', [Helper\RedirectVideoController::class,'getVideo']);
// });
#==========================Student================================

#==========================Admin/Mentor================================
Route::group(['prefix' => 'api/admin'], function () {
    Route::get('/', [Helper\AdminController::class,'dashboard']);
});

// Route::group(['prefix' => 'api/admin/profile'], function () {
//     Route::get('/', [User\AdminController::class,'statUser']);
// });

Route::group(['prefix' => 'api/admin/banner'], function () {
    Route::get('/',         [Banner\BannerController::class,'allData']);
    Route::post('/',         [Banner\BannerController::class,'addData']);
    Route::get('/detail',   [Banner\BannerController::class,'detailDatas']);
    Route::post('/update',   [Banner\BannerController::class,'updateData']);
    Route::delete('/',   [Banner\BannerController::class,'deleteData']);
});

Route::group(['prefix' => 'api/admin/schedule'], function () {
    Route::get('/',         [Banner\BannerController::class,'GetContentSchedule']);
});

Route::group(['prefix' => 'api/admin/sorter'], function () {
    Route::get('/',         [Helper\SorterController::class,'showData']);
    Route::post('/',         [Helper\SorterController::class,'setNumbering']);
    Route::post('/auto',         [Helper\SorterController::class,'setAutoNumbering']);
});

Route::group(['prefix' => 'api/admin/word'], function () {
    Route::get('/',         [Banner\WordController::class,'getContentSchedule']);
    Route::post('/',         [Banner\WordController::class,'addDataWord']);
    Route::get('/detail',   [Banner\WordController::class,'detailDataWords']);
    Route::post('/update',   [Banner\WordController::class,'updateDataWord']);
    Route::delete('/',   [Banner\WordController::class,'deleteData']);
    Route::get('/schedule',   [Banner\WordController::class,'allDataWordByDate']);
});

Route::group(['prefix' => 'api/admin/videos'], function () {
    Route::get('/',         [Banner\VideoController::class,'getContentSchedule']);
    Route::post('/',         [Banner\VideoController::class,'addDataVideo']);
    Route::get('/detail',   [Banner\VideoController::class,'detailDataVideos']);
    Route::post('/update',   [Banner\VideoController::class,'updateDataVideo']);
    Route::delete('/',   [Banner\VideoController::class,'deleteData']);
    Route::get('/schedule',   [Banner\VideoController::class,'allDataVideoByDate']);
});

Route::group(['prefix' => 'api/admin/theme'], function () {
    Route::get('/',         [Post\ThemeAdminController::class,'allData']);
    Route::post('/',         [Post\ThemeAdminController::class,'addData']);
    Route::get('/detail',   [Post\ThemeAdminController::class,'detailData']);
    Route::post('/update',   [Post\ThemeAdminController::class,'updateData']);
    //Route::delete('/',   [Banner\VideoController::class,'deleteData']);
});

Route::group(['prefix' => 'api/admin/forum'], function () {
    Route::get('/',         [Post\PostAdminController::class,'listForum']);
    Route::post('/select',         [Post\PostAdminController::class,'selectForum']);

    Route::post('/post',            [Post\PostController::class,'addForumPost']);##
    Route::post('/update',            [Post\PostController::class,'updateForumPost']);##
    Route::delete('/post',          [Post\PostController::class,'deletePost']);#
    Route::post('/comment',         [Post\PostController::class,'addComment']);##
    Route::delete('/comment',       [Post\PostController::class,'deleteComment']);
    Route::post('/post/alert',      [Post\PostController::class,'alertPost']);#
    Route::delete('/post/alert',      [Post\PostController::class,'alertPostDelete']);
    Route::post('/comment/alert',   [Post\PostController::class,'alertComment']);#
    Route::delete('/comment/alert',   [Post\PostController::class,'alertCommentDelete']);#
    Route::post('/like',            [Post\PostController::class,'addLike']);
    Route::delete('/like',          [Post\PostController::class,'deleteLike']);

    Route::get('/detail',           [Helper\ShowController::class,'forumDetail']);
});

Route::group(['prefix' => 'api/admin/qna'], function () {
    Route::get('/',         [Post\PostAdminController::class,'listQnA']);
    Route::get('/class-list',         [Post\PostAdminController::class,'listClasses']);
    Route::delete('/post',          [Post\PostController::class,'deletePost']);#
    Route::post('/comment',         [Post\PostController::class,'addComment']);##
    Route::delete('/comment',       [Post\PostController::class,'deleteComment']);#
    Route::post('/post/alert',      [Post\PostController::class,'alertPost']);#
    Route::delete('/post/alert',      [Post\PostController::class,'alertPostDelete']);
    Route::post('/comment/alert',   [Post\PostController::class,'alertComment']);#
    Route::delete('/comment/alert',   [Post\PostController::class,'alertCommentDelete']);
    Route::post('/like',            [Post\PostController::class,'addLike']);
    Route::delete('/like',          [Post\PostController::class,'deleteLike']);

    //Route::get('/video',            [Helper\ShowController::class,'qnaByVideo']);
    //Route::get('/posting',          [Helper\ShowController::class,'qnaByUser']);
    Route::get('/detail',           [Helper\ShowController::class,'qnaDetail']);
});

Route::group(['prefix' => 'api/admin/classroom-group'], function () {
    Route::post('/',        [Classes\ClassCategoryController::class,'addData']);
    Route::post('update',   [Classes\ClassCategoryController::class,'updateData']);
    Route::get('/',         [Classes\ClassCategoryController::class,'allData']);
    Route::get('/detail',   [Classes\ClassCategoryController::class,'detailData']);
    Route::delete('/delete',   [Classes\ClassCategoryController::class,'deleteData']);
});


Route::group(['prefix' => 'api/admin/setting'], function () {
    Route::post('/update',        [KkuljaemInfo\InfoController::class,'updateData']);
    Route::get('/',               [KkuljaemInfo\InfoController::class,'getData']);
});

Route::group(['prefix' => 'api/user/notification'], function () {
    Route::post('/update',        [User\UserController::class,'updateDeviceID']);
    //Route::post('/web/update',        [User\WebController::class,'updateDeviceID']);
    Route::get('/',               [Notification\NotificationController::class,'getData']);
    Route::delete('/',               [Notification\NotificationController::class,'deleteData']);
    Route::post('/read',               [Notification\NotificationController::class,'updateRead']);
});

Route::group(['prefix' => 'api/admin/bad-word'], function () {
    Route::get('/',               [BadWord\BadWordController::class,'allData']);
    Route::delete('/',               [BadWord\BadWordController::class,'deleteData']);
    Route::post('/',               [BadWord\BadWordController::class,'addData']);
});
Route::group(['prefix' => 'api/admin/notification'], function () {
    Route::post('/update',        [User\UserController::class,'updateDeviceID']);
    Route::get('/',               [Notification\NotificationController::class,'getData']);
    Route::delete('/',               [Notification\NotificationController::class,'deleteData']);
    Route::post('/read',               [Notification\NotificationController::class,'updateRead']);
});

Route::group(['prefix' => 'api/setting'], function () {
    //Route::post('/update',        [KkuljaemInfo\InfoController::class,'updateData']);
    Route::get('/',               [KkuljaemInfo\InfoController::class,'getData']);
});

Route::group(['prefix' => 'api/admin/testimoni'], function () {
    Route::delete('/',        [Testimoni\TestimoniController::class,'deleteData']);
    Route::get('/',           [Testimoni\TestimoniController::class,'getData']);
    Route::post('/',           [Testimoni\TestimoniController::class,'addData']);
});

Route::group(['prefix' => 'api/testimoni'], function () {
    //Route::delete('/',        [KkuljaemInfo\InfoController::class,'updateData']);
    Route::get('/',           [KkuljaemInfo\InfoController::class,'getData']);
});

Route::group(['prefix' => 'api/admin/classroom'], function () {
    Route::post('/',        [Classes\ClassController::class,'addData']);
    Route::delete('/',      [Classes\ClassController::class,'deleteData']);##
    Route::post('/update',  [Classes\ClassController::class,'updateData']);
    Route::get('/',         [Classes\ClassController::class,'allData']);
    Route::get('/category', [Classes\ClassController::class,'detailDataForClass']);
    Route::get('/add',      [Classes\ClassController::class,'getForAddData']);
    Route::get('/edit',     [Classes\ClassController::class,'detailData']);
    Route::get('/student',  [Classes\ClassController::class,'studentData']);
});

Route::group(['prefix' => 'api/admin/classroom/content'], function () {
    Route::get('/',             [Classes\ClassController::class,'classContent']);
    Route::get('/add',          [Classes\ClassController::class,'checkData']);

    Route::get('/quiz/all',     [Classes\ContentQuizController::class,'getData']);
    Route::get('/quiz',         [Classes\ContentQuizController::class,'checkData']);
    Route::post('/quiz',        [Classes\ContentQuizController::class,'addData']);
    Route::delete('/quiz',      [Classes\ContentQuizController::class,'deleteData']);#
    Route::get('/quiz/detail',  [Classes\ContentQuizController::class,'detailData']);
    Route::post('/quiz/update', [Classes\ContentQuizController::class,'updateData']);
    Route::get('/video/all',    [Classes\ContentVideoController::class,'getData']);
    Route::get('/video',        [Classes\ContentVideoController::class,'checkData']);
    Route::post('/video',       [Classes\ContentVideoController::class,'addData']);
    Route::delete('/video',     [Classes\ContentVideoController::class,'deleteData']);#
    Route::get('/video/detail', [Classes\ContentVideoController::class,'detailData']);
    Route::post('/video/update',[Classes\ContentVideoController::class,'updateData']);
});

Route::group(['prefix' => 'api/admin/classroom/content/video'], function () {
    Route::get('/task',         [Classes\TaskController::class,'checkData']);
    Route::post('/task',        [Classes\TaskController::class,'addData']);
    Route::delete('/task',      [Classes\TaskController::class,'deleteData']);##
    Route::get('/task/detail',  [Classes\TaskController::class,'detailData']);
    Route::post('/task/update', [Classes\TaskController::class,'updateData']);
});
Route::post('/test', [TestController::class,'test']);

Route::group(['prefix' => 'api/admin/classroom/content/video'], function () {
    Route::get('/shadowing',            [Classes\ShadowingController::class,'checkData']);
    Route::post('/shadowing',           [Classes\ShadowingController::class,'addData']);
    Route::delete('/shadowing',        [Classes\ShadowingController::class,'deleteData']);##
    Route::get('/shadowing/detail',     [Classes\ShadowingController::class,'detailData']);
    Route::post('/shadowing/update',    [Classes\ShadowingController::class,'updateData']);
});

Route::group(['prefix' => 'api/admin/subs'], function () {
    Route::get('/', [Payment\SubsController::class,'subsReport']);
    Route::get('/detail', [Payment\SubsController::class,'subsReportDetail']);
});

Route::group(['prefix' => 'api/admin/reference'], function () {
    Route::get('/',            [Reference\ReferenceController::class,'allDatas']);
    Route::post('/',           [Reference\ReferenceController::class,'addData']);
    Route::delete('/',        [Reference\ReferenceController::class,'deleteData']);##
    Route::get('/detail',     [Reference\ReferenceController::class,'detailData']);
    Route::get('/student',     [Reference\ReferenceController::class,'allDataStudent']);
    Route::post('/update',    [Reference\ReferenceController::class,'updateData']);
});

Route::group(['prefix' => 'api/admin/report'], function () {
    Route::get('/',            [Post\AlertReportController::class,'userReport']);
    Route::get('/detail',      [Post\AlertReportController::class,'detailReport']);
    Route::post('/update',     [Post\AlertReportController::class,'updateReport']);##
});
 

Route::group(['prefix' => 'api/admin/profile'], function () {
    Route::get('/',     [User\UserController::class,'detailUserDataUpdateMentor']);
    Route::post('/',      [User\UserController::class,'updateDataMentors']);
    Route::post('device-id',   [User\UserController::class, 'updateDeviceID']);
});


Route::group(['prefix' => 'api/admin/user'], function () {
    Route::post('/student',     [User\UserController::class,'addData']);
    Route::post('/mentor',      [User\UserController::class,'addData']);
    Route::post('/student/update',     [User\UserController::class,'updateDataStudents']);
    Route::post('/mentor/update',      [User\UserController::class,'updateDataMentor']);
    Route::post('/student/status',     [User\UserController::class,'updateDataStudentsStatus']);
    Route::get('/student',     [User\UserController::class,'detailUserDataUpdate']);
    Route::get('/mentor',      [User\UserController::class,'detailUserDataUpdate']);
    Route::delete('/student',     [User\UserController::class,'userDataDelete']);
    Route::delete('/mentor',      [User\UserController::class,'userDataDelete']);
    Route::get('/student/list',     [User\UserController::class,'studentList']);
    Route::get('/student/lists-test', function (Request $request) {
        return view('Student.home');
    });
    Route::get('/student/lists', function (Request $request) {
        //if ($request->ajax()) {
                $student = Models\User::where('jenis_pengguna',0)->limit(10)->get();
                $arr = [];

                for($i=0;$i<count($student);$i++){
                    $status = 'Belum Terverifikasi';
                    if($student[$i]->email_verified_at != null || $student[$i]->email_verified_at != '' ){
                        $status = 'Terverifikasi';
                    }
                    if(date_format(date_create($student[$i]->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                        $status = 'Member';
                    }
                    $jenis_kel = '-';
                    $tgl_lahir = '-';
                    $alamat = '-';
                    $tempat_lahir = '-';
                    if(count($student[$i]->detailStudent)>0){
                        if($student[$i]->detailStudent[0]->jenis_kel!=null){$jenis_kel = $student[$i]->detailStudent[0]->jenis_kel;}
                        if($student[$i]->detailStudent[0]->tgl_lahir!=null){$tgl_lahir = $student[$i]->detailStudent[0]->tgl_lahir;}
                        if($student[$i]->detailStudent[0]->alamat!=null){$alamat = $student[$i]->detailStudent[0]->alamat;}
                        if($student[$i]->detailStudent[0]->tempat_lahir!=null){$tempat_lahir = $student[$i]->detailStudent[0]->tempat_lahir;}
                    }
        
                    $arr1 = [
                        'status'=>$status,
                        'email'=>$student[$i]->email,
                        'nama'=>$student[$i]->nama,
                        'jenis_kel'=>$jenis_kel,
                        'tgl_lahir'=>$tgl_lahir,
                        'tempat_lahir'=>$tempat_lahir,
                        'alamat'=>$alamat,
                        'user_uuid'=>$student[$i]->uuid,
                    ];
                    $arr[$i] = $arr1;
                }
                

                return DataTables::of(Models\User::where('jenis_pengguna',0)->limit(10)->get())
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])    
                    ->make(true);
            //}
    })->name('std.list'); 

    Route::get('/mentor/list',      [User\UserController::class,'mentorList']);
    Route::get('/student/detail',      [User\UserController::class,'detailData']);
    Route::get('/mentor/detail',      [User\UserController::class,'detailData']);
});
// Route::get('/api/student/detail',      [User\UserController::class,'detailData']);
// Route::get('/mentor/detail',      [User\UserController::class,'detailData']);

Route::group(['prefix' => 'api/admin/classroom/content/quiz'], function () {
    Route::get('/exam',         [Classes\ExamController::class,'checkData']);
    Route::post('/exam',        [Classes\ExamController::class,'addData']);
    Route::delete('/exam',        [Classes\ExamController::class,'deleteData']);##
    Route::get('/exam/detail',  [Classes\ExamController::class,'detailData']);
    Route::post('/exam/update', [Classes\ExamController::class,'updateData']);
});

Route::group(['prefix' => 'api/admin/packet'], function () {
    Route::get('/',         [Packet\PacketController::class,'allDatas']);
    Route::post('/',        [Packet\PacketController::class,'addData']);
    Route::delete('/',        [Packet\PacketController::class,'deleteData']);##
    Route::get('/detail',  [Packet\PacketController::class,'detailData']);
    Route::post('/update', [Packet\PacketController::class,'updateData']);
});

Route::group(['prefix' => 'api/admin/image-delete'], function () {
    Route::delete('/',         [Helper\ImageDeleteController::class,'imageDelete']);
});

Route::get('/test', [TestController::class,'test']);
Route::post('/notification', [Payment\PaymentController::class,'notification']);
Route::get('/completed', [Payment\PaymentController::class,'completed']);
Route::get('/unfinish', [Payment\PaymentController::class,'unfinish']);
Route::get('/failed', [Payment\PaymentController::class,'failed']);
//Route::get('/payment', [Payment\PaymentController::class,'show']);
#==========================Admin/Mentor================================

