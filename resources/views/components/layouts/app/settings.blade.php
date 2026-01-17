@role('admin lppm')
<div class="settings">
    <a href="#" class="btn-floating btn btn-icon btn-primary" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasSettings" aria-controls="offcanvasSettings" aria-label="Theme Settings">
        <!-- Download SVG icon from http://tabler.io/icons/icon/brush -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
            <path d="M3 21v-4a4 4 0 1 1 4 4h-4" />
            <path d="M21 3a16 16 0 0 0 -12.8 10.2" />
            <path d="M21 3a16 16 0 0 1 -10.2 12.8" />
            <path d="M10.6 9a9 9 0 0 1 4.4 4.4" />
        </svg>
    </a>
    <form class="offcanvas offcanvas-start offcanvas-narrow" tabindex="-1" id="offcanvasSettings">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title">Theme Settings</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="d-flex flex-column offcanvas-body">
            <div>
                <div class="mb-4">
                    <label class="form-label">Color mode</label>
                    <p class="form-hint">Choose the color mode for your app.</p>
                    <label class="form-check">
                        <div class="form-selectgroup-item">
                            <input type="radio" name="theme" value="light" class="form-check-input" checked />
                            <div class="form-check-label">Light</div>
                        </div>
                    </label>
                    <label class="form-check">
                        <div class="form-selectgroup-item">
                            <input type="radio" name="theme" value="dark" class="form-check-input" />
                            <div class="form-check-label">Dark</div>
                        </div>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="form-label">Color scheme</label>
                    <p class="form-hint">The perfect color mode for your app.</p>
                    <div class="row g-2">
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="blue" class="form-colorinput-input" />
                                <span class="bg-blue form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="azure" class="form-colorinput-input" />
                                <span class="bg-azure form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="indigo" class="form-colorinput-input" />
                                <span class="bg-indigo form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="purple" class="form-colorinput-input" />
                                <span class="bg-purple form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="pink" class="form-colorinput-input" />
                                <span class="bg-pink form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="red" class="form-colorinput-input" />
                                <span class="bg-red form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="orange" class="form-colorinput-input" />
                                <span class="bg-orange form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="yellow" class="form-colorinput-input" />
                                <span class="bg-yellow form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="lime" class="form-colorinput-input" />
                                <span class="bg-lime form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="green" class="form-colorinput-input" />
                                <span class="bg-green form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="teal" class="form-colorinput-input" />
                                <span class="bg-teal form-colorinput-color"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="cyan" class="form-colorinput-input" />
                                <span class="bg-cyan form-colorinput-color"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Font family</label>
                    <p class="form-hint">Choose the font family that fits your app.</p>
                    <div>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-font" value="sans-serif" class="form-check-input"
                                    checked />
                                <div class="form-check-label">Sans-serif</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-font" value="serif" class="form-check-input" />
                                <div class="form-check-label">Serif</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-font" value="monospace" class="form-check-input" />
                                <div class="form-check-label">Monospace</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-font" value="comic" class="form-check-input" />
                                <div class="form-check-label">Comic</div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Theme base</label>
                    <p class="form-hint">Choose the gray shade for your app.</p>
                    <div>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-base" value="slate" class="form-check-input" />
                                <div class="form-check-label">Slate</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-base" value="gray" class="form-check-input" checked />
                                <div class="form-check-label">Gray</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-base" value="zinc" class="form-check-input" />
                                <div class="form-check-label">Zinc</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-base" value="neutral" class="form-check-input" />
                                <div class="form-check-label">Neutral</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-base" value="stone" class="form-check-input" />
                                <div class="form-check-label">Stone</div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Corner Radius</label>
                    <p class="form-hint">Choose the border radius factor for your app.</p>
                    <div>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-radius" value="0" class="form-check-input" />
                                <div class="form-check-label">0</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-radius" value="0.5" class="form-check-input" />
                                <div class="form-check-label">0.5</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-radius" value="1" class="form-check-input" checked />
                                <div class="form-check-label">1</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-radius" value="1.5" class="form-check-input" />
                                <div class="form-check-label">1.5</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme-radius" value="2" class="form-check-input" />
                                <div class="form-check-label">2</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="space-y mt-auto">
                <button type="button" class="w-100 btn" id="reset-changes">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/rotate -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-1">
                        <path d="M19.95 11a8 8 0 1 0 -.5 4m.5 5v-5h-5" />
                    </svg>
                    Reset changes
                </button>
                <a href="#" class="w-100 btn btn-primary" data-bs-dismiss="offcanvas"> Save </a>
            </div>
        </div>
    </form>
</div>
@endrole
