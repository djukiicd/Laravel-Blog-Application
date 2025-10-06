<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title *
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                                   placeholder="Enter post title..."
                                   required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content *
                            </label>
                            <div class="relative">
                                <textarea name="content" 
                                          id="content"
                                          rows="15"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('content') border-red-500 @enderror"
                                          placeholder="Write your post content here... Type # to see tag suggestions"
                                          required>{{ old('content') }}</textarea>
                                
                                <!-- Tag suggestions dropdown -->
                                <div id="tag-suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-40 overflow-y-auto">
                                    <!-- Suggestions will be populated by JavaScript -->
                                </div>
                            </div>
                            @error('content')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tags
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($tags as $tag)
                                    <label class="flex items-center space-x-2 p-2 border rounded-md hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" 
                                               name="tags[]" 
                                               value="{{ $tag->id }}"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                               {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('tags')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('posts.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentTextarea = document.getElementById('content');
            const tagSuggestions = document.getElementById('tag-suggestions');
            const tagCheckboxes = document.querySelectorAll('input[name="tags[]"]');
            const availableTags = @json($tags->pluck('name', 'id'));
            
            let currentTagQuery = '';
            let cursorPosition = 0;
            
            contentTextarea.addEventListener('input', function(e) {
                const text = e.target.value;
                const cursorPos = e.target.selectionStart;
                
                // Find the last # before cursor
                const beforeCursor = text.substring(0, cursorPos);
                const lastHashIndex = beforeCursor.lastIndexOf('#');
                
                if (lastHashIndex !== -1) {
                    const afterHash = beforeCursor.substring(lastHashIndex + 1);
                    const spaceIndex = afterHash.indexOf(' ');
                    
                    if (spaceIndex === -1) {
                        // No space after #, show suggestions
                        currentTagQuery = afterHash.toLowerCase();
                        cursorPosition = lastHashIndex;
                        showTagSuggestions();
                    } else {
                        hideTagSuggestions();
                    }
                } else {
                    hideTagSuggestions();
                }
            });
            
            contentTextarea.addEventListener('keydown', function(e) {
                if (tagSuggestions.classList.contains('hidden')) return;
                
                const suggestions = tagSuggestions.querySelectorAll('.tag-suggestion');
                const activeSuggestion = tagSuggestions.querySelector('.tag-suggestion.active');
                let activeIndex = -1;
                
                if (activeSuggestion) {
                    activeIndex = Array.from(suggestions).indexOf(activeSuggestion);
                }
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, suggestions.length - 1);
                    updateActiveSuggestion(suggestions, activeIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, 0);
                    updateActiveSuggestion(suggestions, activeIndex);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeSuggestion) {
                        selectTag(activeSuggestion);
                    }
                } else if (e.key === 'Escape') {
                    hideTagSuggestions();
                }
            });
            
            function showTagSuggestions() {
                const filteredTags = Object.entries(availableTags).filter(([id, name]) => 
                    name.toLowerCase().includes(currentTagQuery)
                );
                
                if (filteredTags.length === 0) {
                    hideTagSuggestions();
                    return;
                }
                
                tagSuggestions.innerHTML = filteredTags.map(([id, name]) => 
                    `<div class="tag-suggestion px-3 py-2 hover:bg-gray-100 cursor-pointer" data-tag-id="${id}" data-tag-name="${name}">
                        <span class="font-medium">${name}</span>
                    </div>`
                ).join('');
                
                tagSuggestions.classList.remove('hidden');
                
                // Add click listeners
                tagSuggestions.querySelectorAll('.tag-suggestion').forEach(suggestion => {
                    suggestion.addEventListener('click', function() {
                        selectTag(this);
                    });
                });
            }
            
            function hideTagSuggestions() {
                tagSuggestions.classList.add('hidden');
            }
            
            function updateActiveSuggestion(suggestions, activeIndex) {
                suggestions.forEach((suggestion, index) => {
                    suggestion.classList.toggle('active', index === activeIndex);
                    suggestion.classList.toggle('bg-blue-100', index === activeIndex);
                });
            }
            
            function selectTag(suggestionElement) {
                const tagId = suggestionElement.dataset.tagId;
                const tagName = suggestionElement.dataset.tagName;
                
                // Check the corresponding checkbox
                const checkbox = document.querySelector(`input[name="tags[]"][value="${tagId}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
                
                // Replace the #query with the selected tag name
                const text = contentTextarea.value;
                const beforeCursor = text.substring(0, cursorPosition);
                const afterCursor = text.substring(cursorPosition + 1 + currentTagQuery.length);
                const newText = beforeCursor + '#' + tagName + ' ' + afterCursor;
                
                contentTextarea.value = newText;
                
                // Position cursor after the inserted tag
                const newCursorPos = cursorPosition + tagName.length + 2;
                contentTextarea.setSelectionRange(newCursorPos, newCursorPos);
                contentTextarea.focus();
                
                hideTagSuggestions();
            }
            
            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!contentTextarea.contains(e.target) && !tagSuggestions.contains(e.target)) {
                    hideTagSuggestions();
                }
            });
        });
    </script>
</x-app-layout>
