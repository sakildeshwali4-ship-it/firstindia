<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6 mb-3">
                <label for="title">Webseries Title</label>
                <input id="title" name="title" class="form-control" value="{{ old('title', $series->title) }}" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="slug">Slug</label>
                <input id="slug" name="slug" class="form-control" value="{{ old('slug', $series->slug) }}" placeholder="auto-created if empty">
            </div>
            <div class="col-sm-12 mb-3">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" required>{{ old('description', $series->description) }}</textarea>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="genre">Genre</label>
                <input id="genre" name="genre" class="form-control" value="{{ old('genre', $series->genre) }}" placeholder="Romance, Thriller, Revenge" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="language">Language</label>
                <input id="language" name="language" class="form-control" value="{{ old('language', $series->language) }}" placeholder="Hindi, Tamil, Telugu" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="rating">Rating</label>
                <input id="rating" name="rating" class="form-control" type="number" min="0" max="5" step="0.1" value="{{ old('rating', $series->rating ?? 4.5) }}" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="total_episodes">Total Episodes</label>
                <input id="total_episodes" name="total_episodes" class="form-control" type="number" min="0" value="{{ old('total_episodes', $series->total_episodes ?? 0) }}" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="poster_url">Poster URL</label>
                <input id="poster_url" name="poster_url" class="form-control" type="url" value="{{ old('poster_url', $series->poster_url) }}" placeholder="https://... vertical poster">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="poster_file">Or Upload Poster</label>
                <input id="poster_file" name="poster_file" class="form-control" type="file" accept="image/*">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="cover_url">Cover URL</label>
                <input id="cover_url" name="cover_url" class="form-control" type="url" value="{{ old('cover_url', $series->cover_url) }}" placeholder="https://... cover image">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="cover_file">Or Upload Cover</label>
                <input id="cover_file" name="cover_file" class="form-control" type="file" accept="image/*">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control" required>
                    @foreach (['draft', 'published', 'archived'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $series->status ?? 'published') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="coin_price">Series Coin Price</label>
                <input id="coin_price" name="coin_price" class="form-control" type="number" min="0" value="{{ old('coin_price', $series->coin_price ?? 0) }}">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="number_of_free_episodes">Number of Free Episodes</label>
                <input id="number_of_free_episodes" name="number_of_free_episodes" class="form-control" type="number" min="0" value="{{ old('number_of_free_episodes', $series->number_of_free_episodes ?? 0) }}" required>
            </div>
            <div class="col-sm-12 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_premium" value="1" class="form-check-input" id="is_premium" @checked(old('is_premium', $series->is_premium))>
                    <label class="form-check-label" for="is_premium">Premium webseries</label>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-default" type="submit">{{ $submitLabel }}</button>
            <a class="btn btn-link" href="{{ route('series.index') }}">Cancel</a>
        </div>
    </div>
</div>
