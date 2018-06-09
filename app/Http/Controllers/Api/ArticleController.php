<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    /*
     * 待审文章列表
     */
    public function listing()
    {
        $articles = Article::where('status', 0)->paginate(10);
        return $articles;
    }
}
