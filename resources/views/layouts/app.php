<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MediaMind AI' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center py-4 px-2">
                            <span class="font-semibold text-gray-500 text-2xl">MediaMind AI</span>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="/" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold">Home</a>
                        <a href="/about" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">About</a>
                        <a href="/api/docs" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">API Docs</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-white shadow-lg mt-8">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-600 text-sm">
                    &copy; <?= date('Y') ?> MediaMind AI. All rights reserved.
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="#" class="text-gray-500 hover:text-blue-500 mx-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-500 mx-2">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-500 mx-2">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-500 mx-2">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
