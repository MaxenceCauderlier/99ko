<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

$router = router::getInstance();

// Public
$router->map('GET', '/blog[/?]', 'BlogListController#home', 'blog-home');
$router->map('GET', '/blog/cat-[*:name]-[i:id]/[i:page][/?]', 'BlogListController#categoryPage', 'blog-category-page');
$router->map('GET', '/blog/cat-[*:name]-[i:id].html', 'BlogListController#category', 'blog-category');
$router->map('GET', '/blog/[*:name]-[i:id].html', 'BlogReadController#read', 'blog-read');
$router->map('POST', '/blog/send.html', 'BlogReadController#send', 'blog-send');
$router->map('GET', '/blog/rss.html', 'BlogReadController#rss', 'blog-rss');
$router->map('GET', '/blog/[i:page][/?]', 'BlogListController#page', 'blog-page');

// Categories
$router->map('POST', '/admin/blog/addCategory', 'BlogAdminCategoriesController#addCategory', 'admin-blog-add-category');
$router->map('POST', '/admin/blog/deleteCategory', 'BlogAdminCategoriesController#deleteCategory', 'admin-blog-delete-category');
$router->map('POST', '/admin/blog/editCategory', 'BlogAdminCategoriesController#editCategory', 'admin-blog-edit-category');
$router->map('POST', '/admin/blog/saveCategory/[i:id]', 'BlogAdminCategoriesController#saveCategory', 'admin-blog-save-category');
$router->map('GET', '/admin/blog/listAjaxCategories', 'BlogAdminCategoriesController#listAjaxCategories', 'admin-blog-list-ajax-categories');

// Configuration
$router->map('POST', '/admin/blog/saveConfig', 'BlogAdminConfigController#saveConfig', 'admin-blog-save-config');

// Posts
$router->map('GET', '/admin/blog[/?]', 'BlogAdminPostsController#list', 'admin-blog-list');
$router->map('POST', '/admin/blog/deletePost', 'BlogAdminPostsController#deletePost', 'admin-blog-delete-post');
$router->map('GET', '/admin/blog/editPost/[i:id]?', 'BlogAdminPostsController#editPost', 'admin-blog-edit-post');
$router->map('POST', '/admin/blog/savePost', 'BlogAdminPostsController#savePost', 'admin-blog-save-post');

// Comments
$router->map('POST', '/admin/blog/deleteComment', 'BlogAdminCommentsController#deleteComment', 'admin-blog-delete-comment');
$router->map('POST', '/admin/blog/saveComment', 'BlogAdminCommentsController#saveComment', 'admin-blog-save-comment');
$router->map('GET', '/admin/blog/listComments/[i:id]', 'BlogAdminCommentsController#listComments', 'admin-blog-list-comments');