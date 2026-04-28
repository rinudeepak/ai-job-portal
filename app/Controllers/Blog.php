<?php

namespace App\Controllers;

use App\Models\BlogModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Blog extends BaseController
{
    protected BlogModel $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogModel();
    }

    public function index(): string
    {
        $db = \Config\Database::connect();
        $posts = [];

        if ($db->tableExists('blog_posts')) {
            $posts = $this->blogModel->getPublishedPosts(20);
        }

        return view('blog/index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $slug): string
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists('blog_posts')) {
            throw PageNotFoundException::forPageNotFound();
        }

        $post = $this->blogModel->getPublishedPostBySlug($slug);
        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        $recentPosts = $this->blogModel
            ->where('id !=', (int) $post['id'])
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->findAll(3);

        return view('blog/show', [
            'post' => $post,
            'recentPosts' => $recentPosts,
        ]);
    }
}
