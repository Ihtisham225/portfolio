document.addEventListener('DOMContentLoaded', () => {
    // Initialize Select2
    $('#categories').select2({
        placeholder: 'Select categories',
        allowClear: true
    });

    $('#tags').select2({
        placeholder: 'Select tags',
        allowClear: true,
        tags: true,
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newTag: true
            };
        }
    });

    // Media Library State
    let mediaSelectionType = ''; // 'featured' or 'gallery'
    let selectedGalleryImages = [];
    let currentMediaDirectory = '/';
    let currentMediaPage = 1;
    let currentMediaSearch = '';
    let mediaSearchTimeout = null;

    // Initialize existing gallery images
    $('.gallery-item').each(function() {
        selectedGalleryImages.push($(this).data('path'));
    });

    // Open Media Library with AJAX
    function openMediaLibrary(type) {
        mediaSelectionType = type;
        currentMediaDirectory = '/';
        currentMediaPage = 1;
        currentMediaSearch = '';
        
        $('#mediaLibraryModal').removeClass('hidden');
        
        // Reset search input
        $('#mediaSearch').val('');
        
        // Load media library
        loadMediaLibrary();
    }

    // Load Media Library via AJAX
    function loadMediaLibrary() {
        const loading = $('#mediaLoading');
        const content = $('#mediaLibraryContent');
        const pagination = $('#mediaPagination');
        
        // Show loading
        loading.removeClass('hidden');
        content.html('');
        pagination.addClass('hidden');
        
        // Build query parameters
        const params = {
            directory: currentMediaDirectory,
            page: currentMediaPage,
            per_page: 24
        };
        
        if (currentMediaSearch) {
            params.search = currentMediaSearch;
        }
        
        // Make AJAX request using jQuery
        $.ajax({
            url: '/admin/media/simple',
            type: 'GET',
            data: params,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                // Update breadcrumbs
                updateMediaBreadcrumbs(data.breadcrumbs);
                
                // Update media grid
                updateMediaGrid(data.files);
                
                // Update pagination if needed
                if (data.pagination.total > 0) {
                    updateMediaPagination(data.pagination);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading media library:', error);
                content.html(`
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Error loading media</h3>
                        <p class="mt-1 text-sm text-gray-500">${xhr.responseJSON?.message || error}</p>
                    </div>
                `);
            },
            complete: function() {
                loading.addClass('hidden');
            }
        });
    }

    // Update Breadcrumbs
    function updateMediaBreadcrumbs(breadcrumbs) {
        const container = $('#mediaBreadcrumbs');
        let html = '<span class="text-gray-500">Media Library:</span>';
        
        breadcrumbs.forEach((crumb, index) => {
            const isLast = index === breadcrumbs.length - 1;
            if (isLast) {
                html += `<span class="font-medium text-gray-900">${crumb.name}</span>`;
            } else {
                html += `
                    <button type="button" onclick="navigateToDirectory('${crumb.path}')" 
                            class="text-blue-600 hover:text-blue-800 hover:underline">
                        ${crumb.name}
                    </button>
                    <span class="text-gray-400">/</span>
                `;
            }
        });
        
        container.html(html);
    }

    // Update Media Grid
    function updateMediaGrid(files) {
        const container = $('#mediaLibraryContent');
        
        if (files.length === 0) {
            container.html(`
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No images found</h3>
                    <p class="mt-1 text-sm text-gray-500">${currentMediaSearch ? 'Try a different search term' : 'Upload some images first'}</p>
                </div>
            `);
            return;
        }
        
        let html = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">';
        
        files.forEach(file => {
            const isSelected = selectedGalleryImages.includes(file.path);
            const selectedClass = isSelected ? 'ring-2 ring-blue-500 ring-offset-2' : '';
            
            html += `
                <div class="media-item cursor-pointer border border-gray-200 rounded-lg overflow-hidden hover:border-blue-500 hover:shadow-md transition-all ${selectedClass}"
                     data-path="${file.path}"
                     data-url="${file.url}"
                     title="${file.name} (${file.size})">
                    <div class="relative aspect-w-1 aspect-h-1 bg-gray-100">
                        <img src="${file.url}" 
                             alt="${file.name}"
                             class="object-cover w-full h-32"
                             loading="lazy">
                        ${isSelected ? `
                            <div class="absolute top-2 right-2 bg-blue-500 text-white rounded-full p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        ` : ''}
                    </div>
                    <div class="p-2">
                        <p class="text-xs text-gray-600 truncate" title="${file.name}">${file.name}</p>
                        <p class="text-xs text-gray-400">${file.size}</p>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.html(html);
        
        // Add click handlers
        initMediaLibrarySelection();
    }

    // Update Pagination
    function updateMediaPagination(pagination) {
        const container = $('#mediaPagination');
        container.removeClass('hidden');
        
        const { current_page, last_page, total } = pagination;
        
        let html = `
            <div class="flex-1 flex justify-between sm:hidden">
                <button onclick="changeMediaPage(${current_page - 1})" 
                        ${current_page === 1 ? 'disabled' : ''}
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ${current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                    Previous
                </button>
                <button onclick="changeMediaPage(${current_page + 1})"
                        ${current_page === last_page ? 'disabled' : ''}
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ${current_page === last_page ? 'opacity-50 cursor-not-allowed' : ''}">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">${((current_page - 1) * 24) + 1}</span> to 
                        <span class="font-medium">${Math.min(current_page * 24, total)}</span> of 
                        <span class="font-medium">${total}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;
        
        // Previous button
        html += `
            <button onclick="changeMediaPage(${current_page - 1})"
                    ${current_page === 1 ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        `;
        
        // Page numbers
        for (let i = 1; i <= last_page; i++) {
            if (i === 1 || i === last_page || (i >= current_page - 2 && i <= current_page + 2)) {
                const isActive = i === current_page;
                html += `
                    <button onclick="changeMediaPage(${i})"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ${isActive ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'text-gray-500 hover:bg-gray-50'}">
                        ${i}
                    </button>
                `;
            } else if (i === current_page - 3 || i === current_page + 3) {
                html += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`;
            }
        }
        
        // Next button
        html += `
            <button onclick="changeMediaPage(${current_page + 1})"
                    ${current_page === last_page ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${current_page === last_page ? 'opacity-50 cursor-not-allowed' : ''}">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        `;
        
        html += `
                    </nav>
                </div>
            </div>
        `;
        
        container.html(html);
    }

    // Initialize Media Library Selection
    function initMediaLibrarySelection() {
        $('.media-item').off('click').on('click', function() {
            const imagePath = $(this).data('path');
            const imageUrl = $(this).data('url');
            
            if (mediaSelectionType === 'featured') {
                // Set as featured image
                $('#featuredImagePath').val(imagePath);
                $('#featuredImagePreview').html(`<img src="${imageUrl}" alt="Selected" class="w-full h-48 object-cover rounded-lg">`);
                closeMediaLibrary();
            } else if (mediaSelectionType === 'gallery') {
                // Toggle selection for gallery
                const index = selectedGalleryImages.indexOf(imagePath);
                if (index > -1) {
                    // Remove from selection
                    selectedGalleryImages.splice(index, 1);
                    $(this).removeClass('ring-2 ring-blue-500 ring-offset-2');
                    removeGalleryImageFromPreview(imagePath);
                } else {
                    // Add to selection
                    selectedGalleryImages.push(imagePath);
                    $(this).addClass('ring-2 ring-blue-500 ring-offset-2');
                    addGalleryImage(imagePath, imageUrl);
                }
                
                // Update checkmark
                const checkmark = $(this).find('.absolute.top-2.right-2');
                if (checkmark.length) {
                    checkmark.toggle();
                }
            }
        });
    }

    // Navigation Functions
    window.navigateToDirectory = function(path) {
        currentMediaDirectory = path;
        currentMediaPage = 1;
        loadMediaLibrary();
    };

    window.changeMediaPage = function(page) {
        if (page < 1 || page > Math.ceil(selectedGalleryImages.length / 24)) return;
        currentMediaPage = page;
        loadMediaLibrary();
    };

    // Search functionality
    $('#mediaSearch').on('input', function(e) {
        clearTimeout(mediaSearchTimeout);
        mediaSearchTimeout = setTimeout(() => {
            currentMediaSearch = $(this).val();
            currentMediaPage = 1;
            loadMediaLibrary();
        }, 500);
    });

    // Add Gallery Image
    function addGalleryImage(path, url) {
        // Check if image is already added (to avoid duplicates)
        const existingInputs = $(`input[name="gallery_paths[]"][value="${path}"]`);
        if (existingInputs.length > 0) return;
        
        const galleryPreview = $('#galleryPreview');
        const container = $('#galleryPathsContainer');
        
        // Create preview element
        const item = $(`
            <div class="relative gallery-item" data-path="${path}">
                <img src="${url}" alt="Gallery Image" class="w-full h-20 object-cover rounded-lg">
                <button type="button" class="remove-gallery-item absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `);
        
        // Create hidden input
        const input = $(`<input type="hidden" name="gallery_paths[]" value="${path}">`);
        
        // Add to DOM
        galleryPreview.append(item);
        container.append(input);
        
        // Add remove handler
        item.find('.remove-gallery-item').on('click', function() {
            removeGalleryImage($(this));
        });
    }

    // Remove Gallery Image from Preview (without button parameter)
    function removeGalleryImageFromPreview(path) {
        $(`.gallery-item[data-path="${path}"]`).remove();
        $(`input[name="gallery_paths[]"][value="${path}"]`).remove();
    }

    // Remove Gallery Image (with button parameter)
    window.removeGalleryImage = function(button) {
        const item = $(button).closest('.gallery-item');
        const path = item.data('path');
        
        // Remove from selected array
        const index = selectedGalleryImages.indexOf(path);
        if (index > -1) {
            selectedGalleryImages.splice(index, 1);
        }
        
        // Remove from media library selection
        $(`.media-item[data-path="${path}"]`).removeClass('ring-2 ring-blue-500 ring-offset-2');
        $(`.media-item[data-path="${path}"] .absolute.top-2.right-2`).hide();
        
        // Remove preview and input
        item.remove();
        $(`input[name="gallery_paths[]"][value="${path}"]`).remove();
    };

    window.clearGallery = function() {
        $('#galleryPreview').html('');
        $('#galleryPathsContainer').html('');
        selectedGalleryImages = [];
        
        // Clear all selections in media library
        $('.media-item').removeClass('ring-2 ring-blue-500 ring-offset-2');
        $('.media-item .absolute.top-2.right-2').hide();
    };

    window.previewUploadedImage = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#featuredImagePreview').html(`<img src="${e.target.result}" alt="Preview" class="w-full h-48 object-cover rounded-lg">`);
                // Clear media library path if uploading new file
                $('#featuredImagePath').val('');
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.handleGalleryUpload = function(input) {
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const galleryPreview = $('#galleryPreview');
                    const item = $(`
                        <div class="relative gallery-item" data-temp-id="temp">
                            <img src="${e.target.result}" alt="Uploaded" class="w-full h-20 object-cover rounded-lg">
                            <button type="button" class="remove-gallery-item absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    `);
                    
                    galleryPreview.append(item);
                    
                    // Add remove handler
                    item.find('.remove-gallery-item').on('click', function() {
                        $(this).closest('.gallery-item').remove();
                    });
                };
                reader.readAsDataURL(file);
            });
        }
    };

    window.removeFeaturedImage = function() {
        $('#featuredImagePath').val('');
        $('#featuredImagePreview').html('<div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center"><span class="text-gray-400">No image selected</span></div>');
        $('#image').val('');
        if ($('#removeImageFlag').length) {
            $('#removeImageFlag').val('1');
        }
    };

    // Close modal on click outside
    $('#mediaLibraryModal').on('click', function(e) {
        if (e.target === this) {
            closeMediaLibrary();
        }
    });

    $('#closeMediaModal').on('click', closeMediaLibrary);

    function closeMediaLibrary() {
        $('#mediaLibraryModal').addClass('hidden');
    }

    // Make functions available globally
    window.openMediaLibrary = openMediaLibrary;
});