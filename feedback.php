<?php
session_start();

if (!isset($_SESSION['feedback_url'])) {
    header('Location:index.php');
    exit;
}

require "helpers.php";


$errors = [];

$message = '';

$userName = '';

$userlist = json_decode(file_get_contents("./users.json"), true);

foreach ($userlist as $user) {
    if ($user['feedback_url'] == $_SESSION['feedback_url']) {
        $userName = $user['name'];
    } else {
        header('Location:index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['feedback'])) {
        $errors['error'] = 'Please provide a message.';
    } else {
        $feedback = sanitize($_POST['feedback']);
    }

    if (empty($errors)) {
        $new_message = [
            "feedback_url" => $_SESSION['feedback_url'],
            "message" => $_POST['feedback']
        ];

        if (filesize("feedback.json") == 0) {
            $first_record = array($new_message);
            $data_to_save = $first_record;
        } else {
            $old_messages = json_decode(file_get_contents("feedback.json"));
            array_push($old_messages, $new_message);
            $data_to_save = $old_messages;
        }

        $encoded_data = json_encode($data_to_save, JSON_PRETTY_PRINT);

        if (!file_put_contents("feedback.json", $encoded_data, LOCK_EX)) {
            $errors['error'] = "Error storing message, please try again";
        } else {
            $_SESSION['success_message'] = true;
            header('Location:feedback-success.php');
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruthWhisper - Anonymous Feedback App</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>

<body class="bg-gray-100">
    <header class="bg-white">
        <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="index.php" class="-m-1.5 p-1.5">
                    <span class="sr-only">TruthWhisper</span>
                    <span class="block font-bold text-lg bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</span>
                </a>
            </div>
        </nav>
        <!-- Mobile menu, show/hide based on menu open state. -->
        <div class="lg:hidden" role="dialog" aria-modal="true">
            <!-- Background backdrop, show/hide based on slide-over state. -->
            <div class="fixed inset-0 z-10"></div>
            <div class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <a href="index.php" class="-m-1.5 p-1.5">
                        <span class="sr-only">TruthWhisper</span>
                        <span class="block font-bold text-xl bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</span>
                    </a>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="">
        <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
            <img src="./images/beams.jpg" alt="" class="absolute top-1/2 left-1/2 max-w-none -translate-x-1/2 -translate-y-1/2" width="1308" />
            <div class="absolute inset-0 bg-[url(./images/grid.svg)] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
            <div class="relative bg-white px-6 pt-10 pb-8 shadow-xl ring-1 ring-gray-900/5 sm:mx-auto sm:max-w-lg sm:rounded-lg sm:px-10">

                <?php
                $message = flash('success');
                if ($message) :
                ?>
                    <div class="mt-2 bg-teal-500 text-sm text-white rounded-lg p-4" role="alert">
                        <span class="font-bold">
                            <?= $message; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors['error'])) : ?>
                    <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                        <span class="font-bold"><?= $errors['error']; ?></span>
                    </div>
                <?php endif; ?>

                <div class="mx-auto max-w-xl">
                    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
                        <div class="mx-auto w-full max-w-xl text-center">
                            <h1 class="block text-center font-bold text-2xl bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</h1>
                            <h3 class="text-gray-500 my-2">Want to ask something or share a feedback to "<?= $userName; ?>"?</h3>
                        </div>

                        <div class="mt-10 mx-auto w-full max-w-xl">
                            <form class="space-y-6" action="" method="POST" novalidate>
                                <div>
                                    <label for="feedback" class="block text-sm font-medium leading-6 text-gray-900">Don't hesitate, just do it!</label>
                                    <div class="mt-2">
                                        <textarea required name="feedback" id="feedback" cols="30" rows="10" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center justify-center lg:px-8">
            <p class="text-center text-xs leading-5 text-gray-500">&copy; 2024 TruthWhisper, Inc. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>