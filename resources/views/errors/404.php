<?php
$content = <<<HTML
<div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
        <span class="block">Page not found</span>
    </h2>
    <p class="mt-4 text-lg leading-6 text-gray-600">
        Oops! The page you're looking for doesn't exist or has been moved.
    </p>
    <div class="mt-10 flex justify-center">
        <div class="inline-flex rounded-md shadow">
            <a href="/" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Go back home
            </a>
        </div>
        <div class="ml-4 inline-flex">
            <a href="/contact" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                Contact support
            </a>
        </div>
    </div>
    <div class="mt-12">
        <div class="max-w-lg mx-auto overflow-hidden bg-gray-100 rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900">Looking for something else?</h3>
                <div class="mt-3 text-sm text-gray-500">
                    <p>Here are some helpful links instead:</p>
                </div>
                <div class="mt-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="/about" class="text-blue-600 hover:text-blue-500">About Us</a>
                        </li>
                        <li>
                            <a href="/api/docs" class="text-blue-600 hover:text-blue-500">API Documentation</a>
                        </li>
                        <li>
                            <a href="/contact" class="text-blue-600 hover:text-blue-500">Contact Support</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;

// Include the layout
extract(get_defined_vars());
include __DIR__ . '/../layouts/app.php';
?>
