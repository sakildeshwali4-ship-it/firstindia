<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6 mb-3">
                <label for="number">Episode Number</label>
                <input id="number" name="number" class="form-control" type="number" min="1" value="{{ old('number', $episode->number) }}" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="duration_seconds">Duration Seconds</label>
                <input id="duration_seconds" name="duration_seconds" class="form-control" type="number" min="1" max="3600" value="{{ old('duration_seconds', $episode->duration_seconds ?? 60) }}" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="title">Episode Title</label>
                <input id="title" name="title" class="form-control" value="{{ old('title', $episode->title) }}" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="synopsis">Synopsis</label>
                <textarea id="synopsis" name="synopsis" class="form-control" rows="4" required>{{ old('synopsis', $episode->synopsis) }}</textarea>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="thumbnail_url">Thumbnail URL</label>
                <input id="thumbnail_url" name="thumbnail_url" class="form-control" type="url" value="{{ old('thumbnail_url', $episode->thumbnail_url) }}" placeholder="https://...">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="thumbnail_file">Or Upload Thumbnail</label>
                <input id="thumbnail_file" name="thumbnail_file" class="form-control" type="file" accept="image/*">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="video_url">Vertical Video URL</label>
                <input id="video_url" name="video_url" class="form-control" type="url" value="{{ old('video_url', $episode->video_url) }}" placeholder="https://cdn.example.com/episode-1.mp4">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="video_file">Or Upload Video</label>
                <input id="video_file" name="video_file" class="form-control" type="file" accept="video/mp4,video/quicktime,video/x-m4v">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="likes">Initial Likes</label>
                <input id="likes" name="likes" class="form-control" type="number" min="0" value="{{ old('likes', $episode->likes ?? 0) }}" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label for="published_at">Published At</label>
                <input id="published_at" name="published_at" class="form-control" type="datetime-local" value="{{ old('published_at', optional($episode->published_at)->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="col-sm-6 mb-3">
                <label for="coin_price">Episode Coin Price</label>
                <input id="coin_price" name="coin_price" class="form-control" type="number" min="0" value="{{ old('coin_price', $episode->coin_price ?? 0) }}">
            </div>
            <div class="col-sm-6 mb-3">
                <label class="d-block">Access</label>
                <div class="border rounded px-3 py-2 h-100 d-flex align-items-center">
                    <div class="form-check mb-0">
                        <input type="checkbox" name="is_locked" value="1" class="form-check-input" id="is_locked" @checked(old('is_locked', $episode->is_locked))>
                        <label class="form-check-label" for="is_locked">Lock this episode for premium users</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mb-3">
                <label class="d-block">Reel Button Visibility</label>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input type="hidden" name="show_like_button" value="0">
                            <input type="checkbox" name="show_like_button" value="1" class="form-check-input" id="show_like_button" @checked(old('show_like_button', $episode->show_like_button ?? true))>
                            <label class="form-check-label" for="show_like_button">Show like button</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input type="hidden" name="show_watchlist_button" value="0">
                            <input type="checkbox" name="show_watchlist_button" value="1" class="form-check-input" id="show_watchlist_button" @checked(old('show_watchlist_button', $episode->show_watchlist_button ?? true))>
                            <label class="form-check-label" for="show_watchlist_button">Show watchlist button</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input type="hidden" name="show_share_button" value="0">
                            <input type="checkbox" name="show_share_button" value="1" class="form-check-input" id="show_share_button" @checked(old('show_share_button', $episode->show_share_button ?? true))>
                            <label class="form-check-label" for="show_share_button">Show share button</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input type="hidden" name="show_episodes_button" value="0">
                            <input type="checkbox" name="show_episodes_button" value="1" class="form-check-input" id="show_episodes_button" @checked(old('show_episodes_button', $episode->show_episodes_button ?? true))>
                            <label class="form-check-label" for="show_episodes_button">Show episodes button</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-default" type="submit">{{ $submitLabel }}</button>
            <a class="btn btn-link" href="{{ route('series.show', $series) }}">Cancel</a>
        </div>
    </div>
</div>
