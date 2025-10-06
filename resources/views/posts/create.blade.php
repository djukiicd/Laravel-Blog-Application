<x-app-layout>
    <x-slot name="header">
        <h2 class="form-page-header">
            {{ __('Create New Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="form-page-container">
            <div class="form-card">
                <div class="form-body">
                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-field">
                            <label for="title" class="form-label">
                                Title *
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title') }}"
                                   class="@error('title') form-input-error @else form-input @enderror"
                                   placeholder="Enter post title..."
                                   required>
                            @error('title')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="content" class="form-label">
                                Content *
                            </label>
                            <div class="form-relative">
                                <textarea name="content" 
                                          id="content"
                                          rows="15"
                                          class="@error('content') form-textarea-error @else form-textarea @enderror"
                                          placeholder="Write your post content here... Type # to see tag suggestions"
                                          required>{{ old('content') }}</textarea>
                                
                                <!-- Tag suggestions dropdown -->
                                <div id="tag-suggestions" class="tag-suggestions">
                                    <!-- Suggestions will be populated by JavaScript -->
                                </div>
                            </div>
                            @error('content')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">
                                Tags
                            </label>
                            <div class="tag-checkbox-container">
                                @foreach($tags as $tag)
                                    <label class="tag-checkbox-item">
                                        <input type="checkbox" 
                                               name="tags[]" 
                                               value="{{ $tag->id }}"
                                               class="tag-checkbox"
                                               {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                        <span class="tag-checkbox-label">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('tags')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('posts.index') }}" 
                               class="form-clear-button">
                                Cancel
                            </a>
                            
                            <button type="submit" 
                                    class="form-submit-button">
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
                    `<div class="tag-suggestion-item" data-tag-id="${id}" data-tag-name="${name}">
                        <span class="font-medium">${name}</span>
                    </div>`
                ).join('');
                
                tagSuggestions.classList.remove('hidden');
                
                // Add click listeners
                tagSuggestions.querySelectorAll('.tag-suggestion-item').forEach(suggestion => {
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
