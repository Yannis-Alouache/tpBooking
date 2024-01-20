<?php
include_once("Template.php");

class Footer extends Template {
    public function render($context) : string {
        return '
                    <footer class="dark:bg-gray-900">
						<div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
							<div class="sm:flex sm:items-center sm:justify-between">
								<a href="/home" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
									<img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
									<span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">TpBooking</span>
								</a>	
							</div>
							<hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
							<span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2023 <a href="/home" class="hover:underline">TpBooking</a>. All Rights Reserved.</span>
						</div>
                    </footer>
                    
                    <script src="/assets/js/flowbite.js"></script>
                    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
                </body>
            </html>
        ';
    }
}
