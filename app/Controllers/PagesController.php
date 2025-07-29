<?php

namespace App\Controllers;

use App\Models\PageModel;
use App\Models\CategoryModel;
use App\Models\SettingModel;

class PagesController extends BaseController
{
    protected $pageModel;
    protected $categoryModel;
    protected $settingModel;

    public function __construct()
    {
        $this->pageModel = new PageModel();
        $this->categoryModel = new CategoryModel();
        $this->settingModel = new SettingModel();
    }

    /**
     * Display a page by slug
     */
    public function show($slug)
    {
        $page = $this->pageModel->getBySlug($slug);
        
        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        // Get common data for layout
        $categories = $this->categoryModel->getActiveCategories();
        $settings = $this->settingModel->getAllSettings();
        
        // Get navigation pages
        $headerPages = $this->pageModel->getHeaderPages();
        $footerPages = $this->pageModel->getFooterPages();

        $data = [
            'page' => $page,
            'title' => $page['meta_title'] ?: $page['title'],
            'meta_description' => $page['meta_description'],
            'meta_keywords' => $page['meta_keywords'],
            'categories' => $categories,
            'settings' => $settings,
            'headerPages' => $headerPages,
            'footerPages' => $footerPages
        ];

        // Choose template based on page template setting
        $template = $page['template'] ?: 'default';
        
        switch ($template) {
            case 'contact':
                return view('pages/contact', $data);
            case 'about':
                return view('pages/about', $data);
            default:
                return view('pages/default', $data);
        }
    }

    /**
     * Handle contact form submission
     */
    public function submitContact()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[5]|max_length[200]',
            'message' => 'required|min_length[10]|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Here you can add email sending logic or save to database
        // For now, we'll just show a success message
        
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');

        // You can implement email sending here
        // Example: send email to admin
        
        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
