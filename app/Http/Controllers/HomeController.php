<?php

namespace MediaMindAI\Http\Controllers;

use MediaMindAI\Core\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \MediaMindAI\Core\Http\Response
     */
    public function index()
    {
        return $this->view('home', [
            'title' => 'Welcome to MediaMind AI',
            'features' => [
                'Automated Content Generation',
                'Smart Content Curation',
                'Multimedia Enhancement',
                'Audience Intelligence',
                'Workflow Automation'
            ]
        ]);
    }

    /**
     * Show the about page.
     *
     * @return \MediaMindAI\Core\Http\Response
     */
    public function about()
    {
        return $this->view('about', [
            'title' => 'About MediaMind AI',
            'description' => 'MediaMind AI is an integrated artificial intelligence platform designed specifically for media organizations to streamline content creation, distribution, and audience engagement.'
        ]);
    }

    /**
     * Handle the contact form submission.
     *
     * @param  \MediaMindAI\Core\Http\Request  $request
     * @return \MediaMindAI\Core\Http\Response
     */
    public function contact(Request $request)
    {
        $validated = $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:10'
        ]);

        // In a real application, you would process the form submission here
        // For example, send an email or save to the database

        return $this->json([
            'success' => true,
            'message' => 'Thank you for your message. We will get back to you soon!'
        ]);
    }

    /**
     * Show the API documentation.
     *
     * @return \MediaMindAI\Core\Http\Response
     */
    public function apiDocs()
    {
        return $this->json([
            'endpoints' => [
                'GET /api/content' => 'List all content',
                'POST /api/content' => 'Create new content',
                'GET /api/content/{id}' => 'Get specific content',
                'PUT /api/content/{id}' => 'Update content',
                'DELETE /api/content/{id}' => 'Delete content',
            ],
            'authentication' => [
                'type' => 'Bearer Token',
                'description' => 'Include your API token in the Authorization header'
            ]
        ]);
    }

    /**
     * Handle 404 errors.
     *
     * @return \MediaMindAI\Core\Http\Response
     */
    public function notFound()
    {
        return $this->view('errors.404', [], 404);
    }
}
