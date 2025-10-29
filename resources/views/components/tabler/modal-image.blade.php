@props([
    'id',
    'src' => null,
    'alt' => '',
    'title' => null,
    'description' => null,
    'wireIgnore' => true,
    'zoomable' => true,
    'downloadable' => false,
    'downloadText' => 'Download',
    'closable' => true,
    'showCounter' => true,
])

@php
    $imageId = $id . '-image';
    $zoomLevel = 1;
@endphp

<x-tabler.modal
    :id="$id"
    :title="$title"
    :wire-ignore="$wireIgnore"
    size="xl"
    centered="true"
    :close-button="$closable"
    scrollable="true"
    class="modal-image-preview"
>
    <x-slot name="body">
        <div class="image-preview-container">
            @if($description)
                <div class="image-description mb-3">
                    <p class="text-muted mb-0">{{ $description }}</p>
                </div>
            @endif

            <div class="image-controls d-flex justify-content-between align-items-center mb-3">
                <div class="image-counter">
                    @if($showCounter)
                        <small class="text-muted">
                            <span x-text="currentIndex + 1"></span> of <span x-text="totalImages"></span>
                        </small>
                    @endif
                </div>

                @if($zoomable || $downloadable)
                    <div class="image-actions d-flex gap-2">
                        @if($zoomable)
                            <button type="button" class="btn btn-sm btn-light" @click="zoomOut">
                                <i class="icon-tabler icon-tabler-zoom-out"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light" @click="resetZoom">
                                100%
                            </button>
                            <button type="button" class="btn btn-sm btn-light" @click="zoomIn">
                                <i class="icon-tabler icon-tabler-zoom-in"></i>
                            </button>
                        @endif

                        @if($downloadable)
                            <button type="button" class="btn btn-sm btn-light" @click="downloadImage">
                                <i class="icon-tabler icon-tabler-download"></i>
                                {{ $downloadText }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <div class="image-viewer-container" x-data="imagePreview()" x-init="init()">
                <div class="image-wrapper">
                    <img
                        :id="'{{ $imageId }}'"
                        :src="currentImage"
                        :alt="'{{ $alt }}'"
                        :style="`transform: scale(${zoomLevel}); transform-origin: center center;`"
                        class="preview-image"
                        x-transition
                        @click="if (zoomable && event.target === event.currentTarget) zoomToggle()"
                    >
                </div>
            </div>

            {{ $slot }}
        </div>
    </x-slot>
</x-tabler.modal>

@once
    @push('scripts')
        <style>
            .modal-image-preview .modal-body {
                padding: 0;
            }

            .image-preview-container {
                padding: 1.5rem;
            }

            .image-viewer-container {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 300px;
                max-height: 70vh;
                overflow: hidden;
                border-radius: 0.5rem;
                background-color: #f8f9fa;
            }

            .image-wrapper {
                position: relative;
                display: inline-block;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .preview-image {
                max-width: 100%;
                max-height: 70vh;
                object-fit: contain;
                transition: transform 0.3s ease;
                border-radius: 0.5rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .image-controls {
                border-bottom: 1px solid #e9ecef;
                padding-bottom: 1rem;
            }

            .image-actions .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
            }

            .modal-image-preview.modal-fullscreen .modal-body {
                padding: 0;
            }

            .modal-image-preview.modal-fullscreen .image-preview-container {
                height: 100vh;
                padding: 1rem;
            }

            .modal-image-preview.modal-fullscreen .image-viewer-container {
                height: calc(100vh - 8rem);
            }

            /* Zoom cursor */
            .zoomable .preview-image {
                cursor: zoom-in;
            }

            .zoomable .preview-image.zoomed {
                cursor: zoom-out;
            }

            /* Loading state */
            .image-loading {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        </style>

        <script>
            function imagePreview() {
                return {
                    currentImage: '{{ $src }}',
                    currentIndex: 0,
                    totalImages: 1,
                    zoomLevel: 1,
                    isZoomed: false,
                    images: ['{{ $src }}'],

                    init() {
                        // Initialize images array if passed via slot
                        const slotImages = this.$el.parentElement.querySelectorAll('[data-image-src]');
                        if (slotImages.length > 0) {
                            this.images = Array.from(slotImages).map(img => img.dataset.imageSrc);
                            this.totalImages = this.images.length;
                        }

                        // Set up keyboard navigation
                        this.$el.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape' && this.isZoomed) {
                                this.resetZoom();
                            } else if (e.key === '+' || e.key === '=') {
                                this.zoomIn();
                            } else if (e.key === '-') {
                                this.zoomOut();
                            }
                        });

                        // Set up image load events
                        const img = this.$el.querySelector('.preview-image');
                        if (img) {
                            img.addEventListener('load', () => {
                                img.classList.remove('loading');
                            });

                            img.addEventListener('error', () => {
                                img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4=';
                            });
                        }
                    },

                    zoomIn() {
                        if (this.zoomLevel < 3) {
                            this.zoomLevel += 0.25;
                            this.isZoomed = this.zoomLevel > 1;
                            this.updateCursor();
                        }
                    },

                    zoomOut() {
                        if (this.zoomLevel > 0.25) {
                            this.zoomLevel -= 0.25;
                            this.isZoomed = this.zoomLevel > 1;
                            this.updateCursor();
                        }
                    },

                    resetZoom() {
                        this.zoomLevel = 1;
                        this.isZoomed = false;
                        this.updateCursor();
                    },

                    zoomToggle() {
                        if (this.zoomLevel === 1) {
                            this.zoomLevel = 2;
                        } else {
                            this.resetZoom();
                        }
                        this.updateCursor();
                    },

                    updateCursor() {
                        const img = this.$el.querySelector('.preview-image');
                        if (img) {
                            if (this.isZoomed) {
                                img.classList.add('zoomed');
                                img.style.cursor = 'zoom-out';
                            } else {
                                img.classList.remove('zoomed');
                                img.style.cursor = 'zoom-in';
                            }
                        }
                    },

                    downloadImage() {
                        const link = document.createElement('a');
                        link.href = this.currentImage;
                        link.download = '{{ basename($src) ?: "image" }}';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    },

                    nextImage() {
                        if (this.currentIndex < this.totalImages - 1) {
                            this.currentIndex++;
                            this.currentImage = this.images[this.currentIndex];
                            this.resetZoom();
                        }
                    },

                    previousImage() {
                        if (this.currentIndex > 0) {
                            this.currentIndex--;
                            this.currentImage = this.images[this.currentIndex];
                            this.resetZoom();
                        }
                    }
                };
            }

            // Global image preview functions
            window.ImagePreviewModal = {
                show(modalId, imageSrc) {
                    const modal = document.getElementById(modalId);
                    if (modal && bootstrap.Modal) {
                        const bsModal = new bootstrap.Modal(modal);

                        // Update image if provided
                        if (imageSrc) {
                            const img = modal.querySelector('.preview-image');
                            if (img) {
                                img.src = imageSrc;
                            }
                        }

                        bsModal.show();
                        return bsModal;
                    }
                },

                updateImage(modalId, imageSrc, description = null) {
                    const modal = document.getElementById(modalId);
                    if (!modal) return;

                    const img = modal.querySelector('.preview-image');
                    if (img && imageSrc) {
                        img.src = imageSrc;
                    }

                    if (description) {
                        const descEl = modal.querySelector('.image-description p');
                        if (descEl) {
                            descEl.textContent = description;
                        }
                    }
                }
            };

            // Touch gestures for mobile zoom
            document.addEventListener('touchstart', function(e) {
                if (e.touches.length === 2) {
                    e.preventDefault();
                }
            });
        </script>
    @endpush
@endonce

{{--
Usage Example:

1. Basic Image Preview:
<x-tabler.modal-image
    id="image-preview"
    src="/path/to/image.jpg"
    alt="Sample Image"
    title="Image Preview"
    description="High resolution image preview"
/>

2. Multiple Images Gallery:
<x-tabler.modal-image
    id="gallery-modal"
    title="Image Gallery"
    :show-counter="true"
    :zoomable="true"
    :downloadable="true"
    download-text="Save Image"
>
    <div class="d-none">
        <img data-image-src="/images/gallery1.jpg" alt="">
        <img data-image-src="/images/gallery2.jpg" alt="">
        <img data-image-src="/images/gallery3.jpg" alt="">
    </div>
</x-tabler.modal-image>

3. No Zoom, Read-only:
<x-tabler.modal-image
    id="readonly-image"
    src="/path/to/readonly.jpg"
    title="Terms & Conditions"
    :zoomable="false"
    :downloadable="false"
    :closable="true"
/>

JavaScript Usage:
window.ImagePreviewModal.show('image-preview', '/path/to/new-image.jpg');
window.ImagePreviewModal.updateImage('gallery-modal', '/path/to/image.jpg', 'New description');
--}}
