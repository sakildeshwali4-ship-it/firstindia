<div class="form-row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Ad</label>
            <select name="ad_id" class="form-control">
                <option value="">Select Ad</option>
                @foreach ($ads as $ad)
                    <option value="{{ $ad->id }}" {{ ($result->ad_id ?? '') == $ad->id ? 'selected' : '' }}>{{ $ad->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Assign Type</label>
            <select name="assignable_type" id="assignable_type" class="form-control">
                <option value="live_tv" {{ ($result->assignable_type ?? 'live_tv') == 'live_tv' ? 'selected' : '' }}>Live TV</option>
                <option value="video" {{ ($result->assignable_type ?? '') == 'video' ? 'selected' : '' }}>Video</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Assign To</label>
            <select name="assignable_id" id="assignable_id" class="form-control"></select>
        </div>
    </div>
</div>

<div class="form-row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Ad Position</label>
            <select name="ad_position" class="form-control">
                <option value="pre_roll" {{ ($result->ad_position ?? '') == 'pre_roll' ? 'selected' : '' }}>Pre Roll</option>
                <option value="mid_roll" {{ ($result->ad_position ?? 'mid_roll') == 'mid_roll' ? 'selected' : '' }}>Mid Roll</option>
                <option value="post_roll" {{ ($result->ad_position ?? '') == 'post_roll' ? 'selected' : '' }}>Post Roll</option>
                <option value="banner" {{ ($result->ad_position ?? '') == 'banner' ? 'selected' : '' }}>Banner</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Sort Order</label>
            <input name="sort_order" type="number" min="0" class="form-control" value="{{ $result->sort_order ?? 1 }}">
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
</div>
