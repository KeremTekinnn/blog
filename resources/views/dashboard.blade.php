<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Buttons-->
    <!-- Button to open modal -->
    <button id="openModalButton" class="flex justify-end bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Add Blog
    </button>

    <!-- Blog Modal -->
    <div id="blogModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Modal Background -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <!-- Modal Content -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- Add Blog Form -->
                <form action="{{ route('posts.store') }}" method="POST" class="p-6">
                    @csrf
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" id="title" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>
                    <div class="mt-4">
                        <label for="body" class="block text-sm font-medium text-gray-700">Body</label>
                        <textarea name="body" id="body" rows="3" class="mt-1 p-2 border rounded-md w-full" required></textarea>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Post
                        </button>
                        <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" id="cancelButton">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Existing Posts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($posts as $post)
            <div class="bg-white rounded-lg shadow-md p-6 min-h">
                <!-- View Mode -->
                <div class="mb-4">
                    <h2 class="text-xl font-semibold">{{ $post->title }}</h2>
                    <p class="text-gray-600 text-sm">By {{ $post->user->name }}, {{ $post->publicationDate }}</p>
                    <p class="mt-4">{{ $post->body }}</p>
                    <!-- Comments Section -->
                    <h2 class="text-xl font-semibold mt-6">Comments:</h2>
                    @forelse($post->comments as $comment)
                        <div class="mt-4">
                            <p class="font-semibold">By {{ $comment->user ? $comment->user->name : 'Unknown User' }}</p>
                            <p>{{ $comment->comment }}</p>
                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                @if(auth()->id() === $comment->user_id)
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold" style="color: red !important;">Delete</button>
                                @endif
                            </form>
                        </div>
                    @empty
                        <p>No comments yet.</p>
                    @endforelse
                    @if($post->user_id == Auth::id())
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4 editButton" data-postid="{{ $post->id }}">Edit</button>
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-4" id="commentButton-{{ $post->id }}">Comment</button>
                    @else
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-4" id="commentButton-{{ $post->id }}">Comment</button>
                    @endif
                </div>
                <!-- Comment Form -->
                <div id="commentForm-{{ $post->id }}" class="hidden">
                    <form action="{{ route('comments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        <div>
                            <textarea name="comment" rows="2" class="mt-1 p-2 border rounded-md w-full" placeholder="Write your comment here..." required></textarea>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Post Comment</button>
                        </div>
                    </form>
                </div>
                <!-- Edit Form -->
                <div id="editForm-{{ $post->id }}" class="hidden">
                    <form action="{{ route('posts.update', $post->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Your input fields for editing -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" class="mt-1 p-2 border rounded-md w-full" value="{{ $post->title }}" required>
                        </div>
                        <div class="mt-4">
                            <label for="body" class="block text-sm font-medium text-gray-700">Body</label>
                            <textarea name="body" id="body" rows="3" class="mt-1 p-2 border rounded-md w-full" required>{{ $post->body }}</textarea>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Save</button>
                            <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded cancelEditButton" data-postid="{{ $post->id }}">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>


<script>
    // JavaScript to handle modal opening
    document.getElementById('openModalButton').addEventListener('click', function () {
        document.getElementById('blogModal').classList.remove('hidden');
    });

    document.getElementById('cancelButton').addEventListener('click', function () {
        document.getElementById('blogModal').classList.add('hidden');
    });

    // Toggle edit form visibility
    document.querySelectorAll('.editButton').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-postid');
            document.getElementById('editForm-' + postId).classList.toggle('hidden');
        });
    });

    // Cancel edit form
    document.querySelectorAll('.cancelEditButton').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-postid');
            document.getElementById('editForm-' + postId).classList.add('hidden');
        });
    });

    // Toggle comment form visibility
    @foreach($posts as $post)
        document.getElementById('commentButton-{{ $post->id }}').addEventListener('click', function() {
            document.getElementById('commentForm-{{ $post->id }}').classList.toggle('hidden');
        });
    @endforeach
</script>
