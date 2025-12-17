<x-admin.layout title="Media Manager">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
                <p class="text-gray-600">Manage your files and media</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Storage Stats -->
                <div class="hidden md:block">
                    <div class="flex items-center space-x-2">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">{{ $storageStats['used'] }}</span> of {{ $storageStats['total'] }} used
                        </div>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div 
                                class="h-2 rounded-full bg-blue-600"
                                style="width: {{ $storageStats['used_percentage'] }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 modal-overlay"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Upload Files</h3>
                    <p class="mt-1 text-sm text-gray-600">Select files to upload to "{{ $directory ?: '/' }}"</p>
                    <button 
                        type="button" 
                        class="close-modal absolute top-4 right-4 text-gray-400 hover:text-gray-500"
                        data-modal="uploadModal"
                    >
                        <x-admin.icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                
                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="directory" value="{{ $directory }}">
                    
                    <div class="px-6 py-4">
                        <!-- File Drop Zone -->
                        <div 
                            id="dropZone" 
                            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors"
                        >
                            <x-admin.icon name="cloud-upload" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                            <p class="text-gray-600 mb-2">Drag & drop files here</p>
                            <p class="text-sm text-gray-500 mb-4">or</p>
                            <input
                                type="file"
                                id="fileInput"
                                name="files[]"
                                multiple
                                class="hidden"
                                accept=".jpg,.jpeg,.png,.gif,.svg,.webp,.bmp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.7z,.tar,.gz,.mp4,.avi,.mov,.wmv,.flv,.mkv,.mp3,.wav,.ogg,.m4a"
                            >
                            <button
                                type="button"
                                id="browseFilesBtn"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Browse Files
                            </button>
                            <p class="mt-2 text-xs text-gray-500">Maximum file size: 10MB per file</p>
                        </div>
                        
                        <!-- File List -->
                        <div id="fileList" class="mt-4 space-y-2"></div>
                        
                        <!-- Progress Bar -->
                        <div id="uploadProgress" class="mt-4 hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progressBar" class="h-2 rounded-full bg-blue-600" style="width: 0%"></div>
                            </div>
                            <div class="text-sm text-gray-600 mt-2 text-center">
                                <span id="progressText">0%</span> • 
                                <span id="uploadStatus">Preparing upload...</span>
                            </div>
                        </div>
                        
                        <!-- Options -->
                        <div class="mt-6 space-y-4">
                            <div>
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="create_thumbnails"
                                        id="create_thumbnails"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Create thumbnails for images</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Creates small, medium, and large versions of uploaded images</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                        <button
                            type="button"
                            class="close-modal px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            data-modal="uploadModal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            id="uploadSubmitBtn"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                        >
                            Upload Files
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Folder Modal -->
    <div id="newFolderModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 modal-overlay"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Create New Folder</h3>
                    <p class="mt-1 text-sm text-gray-600">Create a new folder in "{{ $directory ?: '/' }}"</p>
                    <button 
                        type="button" 
                        class="close-modal absolute top-4 right-4 text-gray-400 hover:text-gray-500"
                        data-modal="newFolderModal"
                    >
                        <x-admin.icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                
                <form id="newFolderForm" method="POST">
                    @csrf
                    <input type="hidden" name="directory" value="{{ $directory }}">
                    
                    <div class="px-6 py-4">
                        <div>
                            <label for="folder_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Folder Name
                            </label>
                            <input
                                type="text"
                                id="folder_name"
                                name="name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter folder name"
                                pattern="^[a-zA-Z0-9-_ ]+$"
                                required
                            >
                            <p class="mt-1 text-xs text-gray-500">Only letters, numbers, spaces, hyphens, and underscores allowed</p>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                        <button
                            type="button"
                            class="close-modal px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            data-modal="newFolderModal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                        >
                            Create Folder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- File Info Modal -->
    <div id="fileInfoModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 modal-overlay"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" id="infoTitle">File Information</h3>
                    <button 
                        type="button" 
                        class="close-modal absolute top-4 right-4 text-gray-400 hover:text-gray-500"
                        data-modal="fileInfoModal"
                    >
                        <x-admin.icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                
                <div class="px-6 py-4">
                    <div id="infoContent">
                        <div class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="text-gray-500 mt-2">Loading file information...</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button
                        type="button"
                        class="close-modal w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                        data-modal="fileInfoModal"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div id="previewModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 modal-overlay"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900" id="previewTitle"></h3>
                    <div class="flex items-center space-x-2">
                        <button
                            type="button"
                            id="copyPreviewUrlBtn"
                            class="p-2 text-gray-400 hover:text-gray-600"
                            title="Copy URL"
                        >
                            <x-admin.icon name="link" class="w-5 h-5" />
                        </button>
                        <button
                            type="button"
                            id="downloadPreviewBtn"
                            class="p-2 text-gray-400 hover:text-gray-600"
                            title="Download"
                        >
                            <x-admin.icon name="arrow-down-tray" class="w-5 h-5" />
                        </button>
                        <button
                            type="button"
                            class="close-modal p-2 text-gray-400 hover:text-gray-600"
                            data-modal="previewModal"
                        >
                            <x-admin.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div id="previewContent" class="flex items-center justify-center min-h-96">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-500">Loading preview...</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <span id="previewSize"></span> • <span id="previewType"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    id="renamePreviewBtn"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
                                >
                                    Rename
                                </button>
                                <button
                                    type="button"
                                    id="deletePreviewBtn"
                                    class="px-3 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Manager Content -->
    <div class="space-y-6">
        <!-- Toolbar -->
        <x-admin.card>
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <!-- Breadcrumbs -->
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li>
                            <a 
                                href="{{ route('admin.media.index', ['directory' => '/']) }}" 
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600"
                            >
                                <x-admin.icon name="folder" class="w-4 h-4 mr-2" />
                                Media Library
                            </a>
                        </li>
                        @foreach($breadcrumbs as $crumb)
                            <li>
                                <div class="flex items-center">
                                    <x-admin.icon name="chevron-right" class="w-5 h-5 text-gray-400 mx-2" />
                                    <a 
                                        href="{{ route('admin.media.index', ['directory' => $crumb['path']]) }}" 
                                        class="text-sm font-medium text-gray-700 hover:text-blue-600"
                                    >
                                        {{ $crumb['name'] }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </nav>

                <!-- Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Filter -->
                    <select
                        id="filterSelect"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Files</option>
                        <option value="images" {{ $filter === 'images' ? 'selected' : '' }}>Images</option>
                        <option value="documents" {{ $filter === 'documents' ? 'selected' : '' }}>Documents</option>
                        <option value="videos" {{ $filter === 'videos' ? 'selected' : '' }}>Videos</option>
                        <option value="audio" {{ $filter === 'audio' ? 'selected' : '' }}>Audio</option>
                        <option value="archives" {{ $filter === 'archives' ? 'selected' : '' }}>Archives</option>
                    </select>

                    <!-- New Folder Button -->
                    <button
                        type="button"
                        class="open-modal px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm"
                        data-modal="newFolderModal"
                    >
                        <x-admin.icon name="folder-plus" class="w-4 h-4 mr-2" />
                        New Folder
                    </button>

                    <!-- Upload Button -->
                    <button
                        type="button"
                        class="open-modal px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center text-sm"
                        data-modal="uploadModal"
                    >
                        <x-admin.icon name="cloud-upload" class="w-4 h-4 mr-2" />
                        Upload
                    </button>
                </div>
            </div>
        </x-admin.card>

        <!-- Storage Stats Mobile -->
        <div class="md:hidden">
            <x-admin.card>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Storage Usage</span>
                        <span class="text-sm text-gray-600">{{ $storageStats['used'] }} of {{ $storageStats['total'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            class="h-2 rounded-full bg-blue-600"
                            style="width: {{ $storageStats['used_percentage'] }}%"
                        ></div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>{{ $storageStats['used_percentage'] }}% used</span>
                        <span>{{ $storageStats['free_percentage'] }}% free</span>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <!-- File Browser -->
        <x-admin.card>
            @if(count($directories) === 0 && count($files) === 0)
                <div class="text-center py-12">
                    <x-admin.icon name="folder-open" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">This folder is empty</h3>
                    <p class="text-gray-600 mb-6">Upload files or create a new folder to get started</p>
                    <div class="flex justify-center space-x-4">
                        <button
                            type="button"
                            class="open-modal px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            data-modal="newFolderModal"
                        >
                            New Folder
                        </button>
                        <button
                            type="button"
                            class="open-modal px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            data-modal="uploadModal"
                        >
                            Upload Files
                        </button>
                    </div>
                </div>
            @else
                <!-- Directories -->
                @if(count($directories) > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Folders</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($directories as $dir)
                                @php
                                    $dirName = basename($dir);
                                    $dirPath = $dir;
                                @endphp
                                <a 
                                    href="{{ route('admin.media.index', ['directory' => $dirPath]) }}"
                                    class="relative border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer group block"
                                >
                                    <!-- Folder Icon -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 mb-2 flex items-center justify-center text-yellow-500">
                                            <x-admin.icon name="folder" class="w-full h-full" />
                                        </div>
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-gray-900 truncate w-full" title="{{ $dirName }}">
                                                {{ $dirName }}
                                            </p>
                                            <p class="text-xs text-gray-500">Folder</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Files -->
                @if(count($files) > 0)
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Files ({{ count($files) }})</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($files as $file)
                                <div 
                                    class="file-item relative border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer group"
                                    data-path="{{ $file['path'] }}"
                                    data-type="{{ $file['type'] }}"
                                >
                                    <!-- File Icon/Thumbnail -->
                                    <div class="flex flex-col items-center">
                                        @if($file['is_image'] && $file['thumbnail'])
                                            <img 
                                                src="{{ $file['thumbnail'] }}" 
                                                alt="{{ $file['name'] }}"
                                                class="w-12 h-12 mb-2 object-cover rounded"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="w-12 h-12 mb-2 flex items-center justify-center text-gray-400">
                                                <i class="{{ $file['icon'] }} text-2xl"></i>
                                            </div>
                                        @endif
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-gray-900 truncate w-full" title="{{ $file['name'] }}">
                                                {{ $file['name'] }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $file['size'] }}</p>
                                        </div>
                                    </div>

                                    <!-- Context Menu -->
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100">
                                        <div class="relative">
                                            <button
                                                type="button"
                                                class="context-menu-btn p-1 text-gray-400 hover:text-gray-600"
                                                data-path="{{ $file['path'] }}"
                                                data-name="{{ $file['name'] }}"
                                                data-type="{{ $file['type'] }}"
                                            >
                                                <x-admin.icon name="dots-vertical" class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </x-admin.card>
    </div>

    <!-- Context Menu -->
    <div 
        id="contextMenu" 
        class="fixed bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 hidden"
        style="min-width: 200px;"
    >
        <input type="hidden" id="contextItemPath">
        <input type="hidden" id="contextItemName">
        <input type="hidden" id="contextItemType">
        
        <button
            type="button"
            class="context-action w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            data-action="preview"
        >
            <x-admin.icon name="eye" class="w-4 h-4 mr-2" />
            Preview
        </button>
        <button
            type="button"
            class="context-action w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            data-action="download"
        >
            <x-admin.icon name="arrow-down-tray" class="w-4 h-4 mr-2" />
            Download
        </button>
        <button
            type="button"
            class="context-action w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            data-action="copy"
        >
            <x-admin.icon name="link" class="w-4 h-4 mr-2" />
            Copy URL
        </button>
        <button
            type="button"
            class="context-action w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            data-action="info"
        >
            <x-admin.icon name="information-circle" class="w-4 h-4 mr-2" />
            Information
        </button>
        <button
            type="button"
            class="context-action w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            data-action="rename"
        >
            <x-admin.icon name="pencil" class="w-4 h-4 mr-2" />
            Rename
        </button>
        <div class="border-t border-gray-200 my-2"></div>
        <button
            type="button"
            class="context-action w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center"
            data-action="delete"
        >
            <x-admin.icon name="trash" class="w-4 h-4 mr-2" />
            Delete
        </button>
    </div>

    @push('styles')
    <style>
        .modal-overlay {
            cursor: pointer;
        }
        
        #fileList .file-item {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        #contextMenu {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Global variables
        let currentPreviewPath = '';
        let currentPreviewUrl = '';
        let contextMenuData = {};

        // ================= MODAL FUNCTIONS =================
        // Open modal
        $(document).on('click', '.open-modal', function(e) {
            e.preventDefault();
            const modalId = $(this).data('modal');
            $('#' + modalId).removeClass('hidden');
            $('body').addClass('overflow-hidden');
            
            // Focus on first input in modal
            $('#' + modalId).find('input[type="text"]:first').focus();
        });

        // Close modal
        $(document).on('click', '.close-modal', function(e) {
            e.preventDefault();
            const modalId = $(this).data('modal');
            $('#' + modalId).addClass('hidden');
            $('body').removeClass('overflow-hidden');
        });

        // Close modal when clicking on overlay
        $(document).on('click', '.modal-overlay', function(e) {
            if ($(e.target).hasClass('modal-overlay')) {
                $(this).closest('.hidden').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        });

        // Close modal with Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('[id$="Modal"]').addClass('hidden');
                $('body').removeClass('overflow-hidden');
                $('#contextMenu').addClass('hidden');
            }
        });

        // ================= UPLOAD FORM =================
        // Browse files button
        $('#browseFilesBtn').on('click', function() {
            $('#fileInput').click();
        });

        // File input change
        $('#fileInput').on('change', function(e) {
            handleFileSelection(e.target.files);
        });

        // Drag and drop
        $('#dropZone').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('border-blue-500 bg-blue-50');
        });

        $('#dropZone').on('dragleave', function() {
            $(this).removeClass('border-blue-500 bg-blue-50');
        });

        $('#dropZone').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('border-blue-500 bg-blue-50');
            handleFileSelection(e.originalEvent.dataTransfer.files);
        });

        function handleFileSelection(files) {
            const fileList = $('#fileList');
            fileList.empty();
            
            if (files.length === 0) return;
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = $(`
                    <div class="file-item flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 flex items-center justify-center bg-white rounded border">
                                <i class="${getFileIcon(file.name)} text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 truncate w-64">${file.name}</p>
                                <p class="text-xs text-gray-500">${formatBytes(file.size)}</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            ${file.type || 'Unknown type'}
                        </div>
                    </div>
                `);
                fileList.append(fileItem);
            }
            
            // Update drop zone
            $('#dropZone').html(`
                <div class="text-green-600 mb-2">
                    <i class="fas fa-check-circle w-12 h-12 mx-auto"></i>
                </div>
                <p class="text-gray-600 mb-2">${files.length} file(s) selected</p>
                <button
                    type="button"
                    id="browseMoreFilesBtn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Add More Files
                </button>
            `);
            
            // Add event listener to new button
            $('#browseMoreFilesBtn').on('click', function() {
                $('#fileInput').click();
            });
        }

        // AJAX form submission for upload
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = $('#uploadSubmitBtn');
            const originalText = submitBtn.html();
            
            // Show progress bar
            $('#uploadProgress').removeClass('hidden');
            $('#progressBar').css('width', '0%');
            $('#progressText').text('0%');
            $('#uploadStatus').text('Starting upload...');
            submitBtn.prop('disabled', true).html(`
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Uploading...
                </div>
            `);
            
            $.ajax({
                url: '{{ route("admin.media.upload") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new XMLHttpRequest();
                    
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = Math.round((e.loaded / e.total) * 100);
                            $('#progressBar').css('width', percentComplete + '%');
                            $('#progressText').text(percentComplete + '%');
                            $('#uploadStatus').text('Uploading... ' + percentComplete + '%');
                        }
                    }, false);
                    
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification('error', response.message || 'Upload failed');
                        resetUploadForm();
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Upload failed';
                    showNotification('error', error);
                    resetUploadForm();
                }
            });
            
            function resetUploadForm() {
                submitBtn.prop('disabled', false).html(originalText);
                $('#uploadProgress').addClass('hidden');
            }
        });

        // ================= NEW FOLDER FORM =================
        $('#newFolderForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html(`
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Creating...
                </div>
            `);
            
            $.ajax({
                url: '{{ route("admin.media.create-directory") }}',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showNotification('success', 'Folder created successfully');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showNotification('error', response.message || 'Failed to create folder');
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.errors?.name?.[0] || xhr.responseJSON?.message || 'Failed to create folder';
                    showNotification('error', error);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // ================= FILE PREVIEW =================
        // Click on file item
        $(document).on('click', '.file-item', function(e) {
            if ($(e.target).closest('.context-menu-btn').length) return;
            
            const path = $(this).data('path');
            const type = $(this).data('type');
            
            if (type === 'image') {
                showFilePreview(path);
            } else {
                showFileInfo(path);
            }
        });

        function showFilePreview(path) {
            currentPreviewPath = path;
            
            // Show loading state
            $('#previewContent').html(`
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-500">Loading preview...</p>
                </div>
            `);
            
            // Show modal first
            $('#previewModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            
            // Get file info
            $.ajax({
                url: '{{ route("admin.media.file-info") }}',
                type: 'POST',
                data: { 
                    path: path,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        currentPreviewUrl = data.url;
                        
                        // Update preview modal
                        $('#previewTitle').text(data.name);
                        $('#previewSize').text(data.size);
                        $('#previewType').text(data.type.charAt(0).toUpperCase() + data.type.slice(1));
                        
                        // Set preview content
                        let previewContent = '';
                        if (data.is_image) {
                            previewContent = `
                                <img 
                                    src="${data.url}" 
                                    alt="${data.name}"
                                    class="max-w-full max-h-96 object-contain"
                                    onerror="this.src='{{ asset('images/placeholder.png') }}'"
                                >
                            `;
                        } else if (data.type === 'video') {
                            previewContent = `
                                <video controls class="max-w-full max-h-96">
                                    <source src="${data.url}" type="${data.mime_type}">
                                    Your browser does not support the video tag.
                                </video>
                            `;
                        } else if (data.type === 'audio') {
                            previewContent = `
                                <audio controls class="w-full">
                                    <source src="${data.url}" type="${data.mime_type}">
                                    Your browser does not support the audio tag.
                                </audio>
                            `;
                        } else {
                            previewContent = `
                                <div class="text-center">
                                    <i class="${getFileIcon(data.name)} text-6xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-600">Preview not available for this file type</p>
                                    <p class="text-sm text-gray-500 mt-2">Click "Download" to view this file</p>
                                </div>
                            `;
                        }
                        
                        $('#previewContent').html(previewContent);
                    } else {
                        $('#previewContent').html(`
                            <div class="text-center py-8">
                                <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
                                <p class="text-gray-700">Failed to load preview</p>
                                <p class="text-sm text-gray-500 mt-1">${response.message || 'File not found'}</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#previewContent').html(`
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
                            <p class="text-gray-700">Failed to load preview</p>
                            <p class="text-sm text-gray-500 mt-1">Please try again</p>
                        </div>
                    `);
                }
            });
        }

        // ================= HELPER FUNCTIONS =================
        function getFileIcon(filename) {
            const extension = filename.split('.').pop().toLowerCase();
            const icons = {
                // Images
                'jpg': 'fas fa-file-image',
                'jpeg': 'fas fa-file-image',
                'png': 'fas fa-file-image',
                'gif': 'fas fa-file-image',
                'svg': 'fas fa-file-image',
                'webp': 'fas fa-file-image',
                'bmp': 'fas fa-file-image',
                
                // Documents
                'pdf': 'fas fa-file-pdf',
                'doc': 'fas fa-file-word',
                'docx': 'fas fa-file-word',
                'xls': 'fas fa-file-excel',
                'xlsx': 'fas fa-file-excel',
                'ppt': 'fas fa-file-powerpoint',
                'pptx': 'fas fa-file-powerpoint',
                'txt': 'fas fa-file-alt',
                
                // Archives
                'zip': 'fas fa-file-archive',
                'rar': 'fas fa-file-archive',
                '7z': 'fas fa-file-archive',
                'tar': 'fas fa-file-archive',
                'gz': 'fas fa-file-archive',
                
                // Videos
                'mp4': 'fas fa-file-video',
                'avi': 'fas fa-file-video',
                'mov': 'fas fa-file-video',
                'wmv': 'fas fa-file-video',
                'flv': 'fas fa-file-video',
                'mkv': 'fas fa-file-video',
                
                // Audio
                'mp3': 'fas fa-file-audio',
                'wav': 'fas fa-file-audio',
                'ogg': 'fas fa-file-audio',
                'm4a': 'fas fa-file-audio',
                
                // Default
                'default': 'fas fa-file'
            };
            
            return icons[extension] || icons['default'];
        }

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // ================= CONTEXT MENU =================
        $(document).on('click', '.context-menu-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const path = $(this).data('path');
            const name = $(this).data('name');
            const type = $(this).data('type');
            
            contextMenuData = {
                path: path,
                name: name,
                type: type
            };
            
            // Update context menu data
            $('#contextItemPath').val(path);
            $('#contextItemName').val(name);
            $('#contextItemType').val(type);
            
            // Position context menu
            const contextMenu = $('#contextMenu');
            const posX = e.pageX;
            const posY = e.pageY;
            
            contextMenu.css({
                left: posX,
                top: posY,
                position: 'absolute'
            }).removeClass('hidden');
            
            // Close context menu when clicking elsewhere
            $(document).one('click', function() {
                contextMenu.addClass('hidden');
            });
        });

        // Context menu actions
        $(document).on('click', '.context-action', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const action = $(this).data('action');
            const path = $('#contextItemPath').val();
            const name = $('#contextItemName').val();
            const type = $('#contextItemType').val();
            
            $('#contextMenu').addClass('hidden');
            
            switch(action) {
                case 'preview':
                    if (type === 'image') {
                        showFilePreview(path);
                    } else {
                        showFileInfo(path);
                    }
                    break;
                    
                case 'download':
                    downloadFile(path, name);
                    break;
                    
                case 'copy':
                    copyFileUrl(path);
                    break;
                    
                case 'info':
                    showFileInfo(path);
                    break;
                    
                case 'rename':
                    renameFile(path, name, type);
                    break;
                    
                case 'delete':
                    deleteFile(path, name, type);
                    break;
            }
        });

        // ================= FILE OPERATIONS =================
        function downloadFile(path, filename) {
            $.ajax({
                url: '{{ route("admin.media.file-info") }}',
                type: 'POST',
                data: { 
                    path: path,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Create temporary link for download
                        const link = document.createElement('a');
                        link.href = response.data.url;
                        link.download = filename || response.data.name;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        showNotification('success', 'Download started');
                    }
                },
                error: function() {
                    showNotification('error', 'Failed to download file');
                }
            });
        }

        function copyFileUrl(path) {
            $.ajax({
                url: '{{ route("admin.media.file-info") }}',
                type: 'POST',
                data: { 
                    path: path,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const url = response.data.url;
                        
                        // Use modern clipboard API
                        navigator.clipboard.writeText(url).then(function() {
                            showNotification('success', 'URL copied to clipboard');
                        }, function() {
                            // Fallback for older browsers
                            const textArea = document.createElement('textarea');
                            textArea.value = url;
                            document.body.appendChild(textArea);
                            textArea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textArea);
                            showNotification('success', 'URL copied to clipboard');
                        });
                    }
                },
                error: function() {
                    showNotification('error', 'Failed to copy URL');
                }
            });
        }

        function showFileInfo(path) {
            // Show loading state
            $('#infoContent').html(`
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-gray-500 mt-2">Loading file information...</p>
                </div>
            `);
            
            $('#fileInfoModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            
            $.ajax({
                url: '{{ route("admin.media.file-info") }}',
                type: 'POST',
                data: { 
                    path: path,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        const isImage = data.is_image;
                        
                        let dimensionsHtml = '';
                        if (data.dimensions) {
                            dimensionsHtml = `
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="font-medium text-gray-900 mb-2">Dimensions</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm text-gray-600">Width:</span>
                                            <span class="ml-2 text-sm font-medium">${data.dimensions.width}px</span>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Height:</span>
                                            <span class="ml-2 text-sm font-medium">${data.dimensions.height}px</span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="text-sm text-gray-600">Aspect Ratio:</span>
                                            <span class="ml-2 text-sm font-medium">${data.dimensions.ratio}:1</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                        
                        $('#infoTitle').text('File Information: ' + data.name);
                        $('#infoContent').html(`
                            <div class="space-y-4">
                                <div class="flex items-center justify-center mb-4">
                                    ${isImage ? 
                                        `<img src="${data.url}" alt="${data.name}" class="max-w-full max-h-48 object-contain rounded">` :
                                        `<i class="${getFileIcon(data.name)} text-4xl text-gray-400"></i>`
                                    }
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-600">Name:</span>
                                        <p class="text-sm font-medium truncate" title="${data.name}">${data.name}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Type:</span>
                                        <p class="text-sm font-medium">${data.type.charAt(0).toUpperCase() + data.type.slice(1)}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Size:</span>
                                        <p class="text-sm font-medium">${data.size}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">MIME Type:</span>
                                        <p class="text-sm font-medium">${data.mime_type}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Extension:</span>
                                        <p class="text-sm font-medium">.${data.extension}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Last Modified:</span>
                                        <p class="text-sm font-medium">${data.last_modified}</p>
                                    </div>
                                </div>
                                
                                ${dimensionsHtml}
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="font-medium text-gray-900 mb-2">Path</h4>
                                    <div class="bg-gray-50 p-3 rounded text-sm font-mono break-all">
                                        ${data.path}
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="font-medium text-gray-900 mb-2">URL</h4>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-50 p-3 rounded text-sm font-mono break-all">
                                            ${data.url}
                                        </div>
                                        <button
                                            type="button"
                                            class="copy-url-btn px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                                            data-url="${data.url}"
                                        >
                                            Copy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `);
                        
                        // Add copy URL button functionality
                        $('.copy-url-btn').on('click', function() {
                            const url = $(this).data('url');
                            navigator.clipboard.writeText(url).then(function() {
                                showNotification('success', 'URL copied to clipboard');
                            }, function() {
                                const textArea = document.createElement('textarea');
                                textArea.value = url;
                                document.body.appendChild(textArea);
                                textArea.select();
                                document.execCommand('copy');
                                document.body.removeChild(textArea);
                                showNotification('success', 'URL copied to clipboard');
                            });
                        });
                    } else {
                        $('#infoContent').html(`
                            <div class="text-center py-8">
                                <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
                                <p class="text-gray-700">Failed to load file information.</p>
                                <p class="text-sm text-gray-500 mt-1">${response.message || 'File not found'}</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#infoContent').html(`
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
                            <p class="text-gray-700">Failed to load file information.</p>
                            <p class="text-sm text-gray-500 mt-1">Please try again</p>
                        </div>
                    `);
                }
            });
        }

        function renameFile(path, currentName, type) {
            const newName = prompt(`Rename ${type === 'file' ? 'file' : 'folder'} "${currentName}" to:`, currentName);
            
            if (!newName || newName === currentName) return;
            
            // Validate filename
            if (!/^[a-zA-Z0-9-_. ]+$/.test(newName)) {
                showNotification('error', 'Invalid filename. Only letters, numbers, spaces, hyphens, underscores, and periods allowed.');
                return;
            }
            
            $.ajax({
                url: '{{ route("admin.media.rename") }}',
                type: 'POST',
                data: {
                    old_path: path,
                    new_name: newName,
                    type: type,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showNotification('error', response.message);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Failed to rename';
                    showNotification('error', error);
                }
            });
        }

        function deleteFile(path, name, type) {
            if (!confirm(`Are you sure you want to delete ${type === 'file' ? 'file' : 'folder'} "${name}"? This action cannot be undone.`)) {
                return;
            }
            
            $.ajax({
                url: '{{ route("admin.media.destroy") }}',
                type: 'DELETE',
                data: {
                    path: path,
                    type: type,
                    confirm: true,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showNotification('success', response.message || 'Deleted successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Failed to delete';
                    showNotification('error', error);
                }
            });
        }

        // ================= PREVIEW MODAL ACTIONS =================
        $(document).on('click', '#copyPreviewUrlBtn', function() {
            if (!currentPreviewUrl) return;
            
            navigator.clipboard.writeText(currentPreviewUrl).then(function() {
                showNotification('success', 'URL copied to clipboard');
            }, function() {
                const textArea = document.createElement('textarea');
                textArea.value = currentPreviewUrl;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showNotification('success', 'URL copied to clipboard');
            });
        });

        $(document).on('click', '#downloadPreviewBtn', function() {
            if (!currentPreviewPath) return;
            
            const filename = currentPreviewPath.split('/').pop();
            downloadFile(currentPreviewPath, filename);
        });

        $(document).on('click', '#renamePreviewBtn', function() {
            if (!currentPreviewPath) return;
            
            $.ajax({
                url: '{{ route("admin.media.file-info") }}',
                type: 'POST',
                data: { 
                    path: currentPreviewPath,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        renameFile(currentPreviewPath, data.name, 'file');
                    }
                }
            });
        });

        $(document).on('click', '#deletePreviewBtn', function() {
            if (!currentPreviewPath) return;
            
            $.ajax({
                url: '{{ route("admin.media.file-info") }}',
                type: 'POST',
                data: { 
                    path: currentPreviewPath,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        deleteFile(currentPreviewPath, data.name, 'file');
                        // Close preview modal after deletion
                        setTimeout(function() {
                            $('#previewModal').addClass('hidden');
                            $('body').removeClass('overflow-hidden');
                        }, 500);
                    }
                }
            });
        });

        // ================= FILTER =================
        $('#filterSelect').on('change', function() {
            const filter = $(this).val();
            const url = new URL(window.location);
            url.searchParams.set('filter', filter);
            window.location.href = url.toString();
        });

        // ================= NOTIFICATION FUNCTION =================
        function showNotification(type, message) {
            // Create notification element
            const notification = $(`
                <div class="fixed top-4 right-4 z-50 animate-fade-in-down">
                    <div class="bg-white rounded-lg shadow-lg border p-4 max-w-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                ${type === 'success' ? 
                                    '<i class="fas fa-check-circle w-5 h-5 text-green-500"></i>' :
                                type === 'error' ?
                                    '<i class="fas fa-exclamation-circle w-5 h-5 text-red-500"></i>' :
                                type === 'warning' ?
                                    '<i class="fas fa-exclamation-triangle w-5 h-5 text-yellow-500"></i>' :
                                    '<i class="fas fa-info-circle w-5 h-5 text-blue-500"></i>'
                                }
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">${message}</p>
                            </div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100">
                                <i class="fas fa-times w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
            
            // Add to body
            $('body').append(notification);
            
            // Auto remove after 3 seconds
            setTimeout(function() {
                notification.remove();
            }, 3000);
            
            // Remove on close button click
            notification.find('button').on('click', function() {
                notification.remove();
            });
        }

        // ================= DRAG & DROP FILE UPLOAD =================
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area when dragging files over
        ['dragenter', 'dragover'].forEach(eventName => {
            document.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            $('body').addClass('drag-over');
        }

        function unhighlight(e) {
            $('body').removeClass('drag-over');
        }

        // Handle dropped files
        document.addEventListener('drop', function(e) {
            if (!$('#uploadModal').hasClass('hidden')) return;
            
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                // Open upload modal and handle files
                $('#uploadModal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
                handleFileSelection(files);
            }
        });

        // ================= INITIALIZATION =================

        // Confirm navigation when upload is in progress
        let uploadInProgress = false;

        $(window).on('beforeunload', function() {
            if (uploadInProgress) {
                return 'File upload is in progress. Are you sure you want to leave?';
            }
        });

        // Add CSS for drag-over state and animations
        $('head').append(`
            <style>
                body.drag-over::before {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(59, 130, 246, 0.1);
                    border: 2px dashed #3b82f6;
                    z-index: 9999;
                    pointer-events: none;
                }
                
                .animate-fade-in-down {
                    animation: fadeInDown 0.3s ease-out;
                }
                
                @keyframes fadeInDown {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            </style>
        `);
    });
</script>
@endpush
</x-admin.layout>