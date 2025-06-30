<?php
$content = <<<HTML
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">$title</h1>
        <div class="prose max-w-none">
            <p class="text-gray-600 text-lg mb-6">$description</p>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8">
                <p class="text-blue-700">
                    <strong>Our Mission:</strong> To empower media organizations with AI-driven tools that enhance creativity, efficiency, and audience engagement.
                </p>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4">Our Vision</h2>
            <p class="text-gray-600 mb-6">
                We envision a future where AI and human creativity work hand in hand to produce exceptional content that informs, entertains, and inspires audiences worldwide.
            </p>
            
            <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4">Why Choose MediaMind AI?</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Cutting-Edge Technology</h3>
                    <p class="text-gray-600">Built with the latest AI and machine learning technologies to deliver superior performance and results.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">User-Centric Design</h3>
                    <p class="text-gray-600">Intuitive interfaces designed with content creators and editors in mind.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Scalable Solutions</h3>
                    <p class="text-gray-600">Solutions that grow with your organization's needs, from small teams to large enterprises.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Data Security</h3>
                    <p class="text-gray-600">Enterprise-grade security to protect your content and data.</p>
                </div>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4">Our Team</h2>
            <p class="text-gray-600 mb-6">
                Our team consists of passionate AI researchers, software engineers, and media professionals dedicated to revolutionizing the media industry through innovation and technology.
            </p>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Get in Touch</h3>
                <p class="text-gray-600 mb-4">Have questions or want to learn more about our platform?</p>
                <a href="/contact" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-300">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>
HTML;

// Include the layout
extract(get_defined_vars());
include __DIR__ . '/layouts/app.php';
?>
