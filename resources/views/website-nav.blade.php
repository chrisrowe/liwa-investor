<div class="bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="row">
            <div class="col-5 col-md-3 col-lg-2">
                <a class="flex-link" href="https://liwacap.com">
                    <figure class="logo masthead small">
                        <img src="../../images/logo.svg">
                    </figure>
                </a>
            </div>
            <div class="col-7 col-md-9 col-lg-10 align-self-center">
                <button id="hamburger"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100
                               focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out float-right lg:hidden"
                        onclick="openCloseModal()"
                >
                    <svg stroke="currentColor"
                          fill="none"
                          viewBox="0 0 24 24"
                          class="h-6 w-6"
                    >
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"
                              id="menu-burger"
                              class="inline-flex"
                        >
                        </path>
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"
                              id="menu-close"
                              class="d-none"
                        >
                        </path>
                    </svg>
                </button>

                <nav id="navigation" class="navigation custom-font-serif" role="navigation" aria-label="Main">
                    <section>
                        <a class="nav" href="https://liwacap.com/what-we-offer">
                            <span>
                                What we offer
                            </span>
                        </a>
                        <a class="nav" href="https://liwacap.com/our-partners">
                            <span>
                                Our partners
                            </span>
                        </a>
                        <a class="nav" href="https://liwacap.com/our-approach">
                            <span>
                                Our approach
                            </span>
                        </a>
                        <a class="nav" href="https://liwacap.com/our-history">
                            <span>
                                Our history
                            </span>
                        </a>
                        <a class="nav" href="https://liwacap.com/our-team">
                            <span>
                                Our team
                            </span>
                        </a>
                        <a class="nav" href="https://liwacap.com/insights">
                            <span>
                                Insights
                            </span>
                        </a>
                    </section>
                    <section>
                        <a href="https://liwacap.com/contact"
                            class="bttn secondary contact-bttn d-none d-lg-inline-block">Contact</a>
                        <a href="javascript:;" class="bttn investor-bttn d-none">INVESTOR LOGIN</a>
                    </section>
                </nav>
            </div>
        </div>
    </div>

    <nav id="navigation-responsive" class="navigation d-none mobile" role="navigation" aria-label="Main">
        <section>
            <a class="nav" href="https://liwacap.com/what-we-offer">
                <span>
                    What we offer
                </span>
            </a>
            <a class="nav" href="https://liwacap.com/our-partners">
                <span>
                    Our partners
                </span>
            </a>
            <a class="nav active" href="https://liwacap.com/our-approach">
                <span>
                    Our approach
                </span>
            </a>
            <a class="nav" href="https://liwacap.com/our-history">
                <span>
                    Our history
                </span>
            </a>
            <a class="nav" href="https://liwacap.com/our-team">
                <span>
                    Our team
                </span>
            </a>
            <a class="nav" href="https://liwacap.com/insights">
                <span>
                    Insights
                </span>
            </a>
            <a class="nav" href="https://liwacap.com/contact">
                <span>
                    Contact
                </span>
            </a>
        </section>
        <section>
            <a href="https://liwacap.com/contact" class="bttn secondary contact-bttn d-none d-lg-block">Contact</a>
            <a href="javascript:;" class="bttn investor-bttn">INVESTOR LOGIN</a>
        </section>
    </nav>
</div>

<script>
    var isOpennedDropdownMenu = false;

    var openCloseModal = function() {
        isOpennedDropdownMenu = !isOpennedDropdownMenu;
        console.log('isOpennedDropdownMenu', isOpennedDropdownMenu);
        var menuBurger = document.getElementById("menu-burger");
        var menuClose = document.getElementById("menu-close");
        var navigationMenu = document.getElementById("navigation-responsive");
        if (isOpennedDropdownMenu) {
            menuBurger.classList.add("d-none");
            menuClose.classList.remove("d-none");
            navigationMenu.classList.remove("d-none");
        } else {
            menuClose.classList.add("d-none");
            menuBurger.classList.remove("d-none");
            navigationMenu.classList.add("d-none");
        }
    };
</script>
