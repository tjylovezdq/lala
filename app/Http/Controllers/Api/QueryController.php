<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use QL\QueryList;
use DB;

class QueryController extends Controller
{
    /**
     * 采集散文
     * category_id: 文章分类
     */
    public function queryArticle(Request $rq)
    {
        $category_id = $rq->category_id;
        if ($category_id == 1) {
            $url = 'http://www.sanwen.com/sanwen/shuqingsanwen/';
        } elseif ($category_id == 2) {
            $url = 'http://www.sanwen.com/sanwen/zhelisanwen/';
        } elseif ($category_id == 3) {
            $url = 'http://www.sanwen.com/sanwen/guxiangsanwen/';
        } elseif ($category_id == 4) {
            $url = 'http://www.sanwen.com/sanwen/shanggansanwen/';
        } elseif ($category_id == 5) {
            $url = 'http://www.sanwen.com/sanwen/aiqingsanwen/';
        } else {
            $url = 'http://www.sanwen.com/sanwen/mingjiasanwen/';
        }

        $rule1 = array(
            'link' => array('.list-base-article a', 'href'),
        );
        $lists = QueryList::get($url)->rules($rule1)->query()->getData();

        foreach ($lists as $k => $list) {
            $link = $list['link'];
            $rule2 = array(
                // 采集标题
                'title' => array('.row-article>h1', 'text'),
                //采集class为article-content这个元素里面的纯文本内容
                'content' => array('.article-content', 'text'),
                //采集class为at_url下面的超链接的链接
                'link' => array('.at_url>a', 'href'),
                //采集class为article-content下面的第一张图片的链接
                'img' => array('.article-content img', 'src'),
                //采集span标签中的HTML内容
                //'other' => array('span','html')
            );
            $query_article = QueryList::get('http://www.sanwen.com' . $link)->rules($rule2)->query()->getData();
            // 写入artcile表
            if (empty($query_article[0])) {
                continue;
            }
            $article = new Article();
            $article->category_id = $category_id;
            $article->title = $query_article[0]['title'];
            // 1.先下载到public/upload下面
            $local_image_path = $this->downloadImage($query_article[0]['img']); // 本地绝对路径
            $qiniu_pic_path = $this->uploadImage($local_image_path);
            $article->cover = $qiniu_pic_path;
            $article->create_time = date('Y-m-d H:i:s', time());
            $article->update_time = date('Y-m-d H:i:s', time());
            $article->save();

            // 写article_content表
            $article_id = $article->id;
            $content_data = array();
            $content_data['article_id'] = $article_id;
            $content_data['content'] = $query_article[0]['content'];
            $content_data['create_time'] = date('Y-m-d H:i:s', time());
            $content_data['update_time'] = date('Y-m-d H:i:s', time());
            DB::table('t_article_content')->insert($content_data);
            echo date('Y-m-d H:i:s', time()) . $article[0]['title'] . '成功<br />';
        }
    }

    /**
     * 下载远程图片到本地
     */
    public function downloadImage($imageUrl)
    {
        $curl = curl_init($imageUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $imageData = curl_exec($curl);
        curl_close($curl);
        $image_name = substr($imageUrl, -22);
        $tp = fopen(public_path() . "/upload/" . $image_name, "a");
        fwrite($tp, $imageData);
        fclose($tp);
        return public_path() . "/upload/" . $image_name;
    }

    /**
     * 图片上传七牛云
     */
    public function uploadImage($url)
    {
        $accessKey = 'hM3AWjHYzht50NBg4HGGZ59yX_g86bAfsBx0-clo';
        $secretKey = 'QRSdVeYDbq1lsUwGdFwk6jpeXIOvhXNY2BVwr-nz';
        $bucket = 'payone';
        $auth = new Auth($accessKey, $secretKey);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        $filePath = $url;
        // 上传到七牛后保存的文件名
        $key = substr($url, -22);
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        $uploadMgr->putFile($token, $key, $filePath);
        return 'p8to9e2ei.bkt.clouddn.com/' . $key;
    }
}
