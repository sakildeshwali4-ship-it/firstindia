@php
    $startDate = $result && $result->start_date ? date('Y-m-d\TH:i', strtotime($result->start_date)) : '';
    $endDate = $result && $result->end_date ? date('Y-m-d\TH:i', strtotime($result->end_date)) : '';
@endphp

<div class="form-row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Title</label>
            <input name="title" type="text" class="form-control" value="{{ $result->title ?? '' }}" placeholder="Enter Ad Title">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control">
                <option value="normal" {{ ($result->type ?? 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="l_band" {{ ($result->type ?? '') == 'l_band' ? 'selected' : '' }}>L Band</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Media Type</label>
            <select name="media_type" id="media_type" class="form-control">
                <option value="image" {{ ($result->media_type ?? 'image') == 'image' ? 'selected' : '' }}>Image</option>
                <option value="video" {{ ($result->media_type ?? '') == 'video' ? 'selected' : '' }}>Video</option>
            </select>
        </div>
    </div>
</div>

<div class="form-row">
    <!-- Image Upload -->
    <div class="col-md-6" id="image_upload_div">
        <div class="form-group">
            <label>Upload Image</label>

            <input type="file"
                   name="media_image"
                   class="form-control"
                   accept="image/*">

            @if(!empty($result->media_url) && ($result->media_type ?? '') == 'image')
                <div class="mt-2">
                    <img src="{{ asset($result->media_url) }}"
                         width="120"
                         class="img-thumbnail">
                </div>
            @endif
        </div>
    </div>

    <!-- Video URL -->
    <div class="col-md-6" id="video_url_div">
        <div class="form-group">
            <label>Video URL</label>

            <input name="media_url"
                   type="text"
                   class="form-control"
                   value="{{ ($result->media_type ?? '') == 'video' ? $result->media_url : '' }}"
                   placeholder="Enter Video URL">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Click URL</label>

            <input name="click_url"
                   type="text"
                   class="form-control"
                   value="{{ $result->click_url ?? '' }}"
                   placeholder="Enter Click URL">
        </div>
    </div>
</div>

<div class="form-row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Start After Seconds</label>
            <input name="start_after_seconds" type="number" min="0" class="form-control" value="{{ $result->start_after_seconds ?? 30 }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Repeat Every Seconds</label>
            <input name="repeat_every_seconds" type="number" min="0" class="form-control" value="{{ $result->repeat_every_seconds ?? 300 }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Duration Seconds</label>
            <input name="duration_seconds" type="number" min="0" class="form-control" value="{{ $result->duration_seconds ?? 15 }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Skippable After Seconds</label>
            <input name="skippable_after_seconds" type="number" min="0" class="form-control" value="{{ $result->skippable_after_seconds ?? 0 }}">
        </div>
    </div>
</div>

<div class="form-row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Priority</label>
            <input name="priority" type="number" min="0" class="form-control" value="{{ $result->priority ?? 1 }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Status</label>
            <select name="active" class="form-control">
                <option value="1" {{ ($result->active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ ($result->active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Start Date</label>
            <input name="start_date" type="datetime-local" class="form-control" value="{{ $startDate }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>End Date</label>
            <input name="end_date" type="datetime-local" class="form-control" value="{{ $endDate }}">
        </div>
    </div>
</div>
