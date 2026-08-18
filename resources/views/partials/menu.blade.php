<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="fs-7 text-white fw-bold">
                            MENUS
                        </span>
                    </div>
                </div>

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fa fa-university"></i>
                        </span>
                        <span class="menu-title text-white">
                            MENU
                        </span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">

                        <div class="menu-item">
                            <a class="menu-link {{ Route::currentRouteName() == 'categoria.listado' ? 'active' : '' }}"
                                href="#">

                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>

                                <span class="menu-title text-white">
                                    Sub menu
                                </span>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
```
