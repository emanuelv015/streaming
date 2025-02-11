<div class="form-group">
    <label for="stream_url">Stream URL</label>
    <input type="url" 
           class="form-control" 
           id="stream_url" 
           name="stream_url" 
           value="<?php echo htmlspecialchars($match['stream_url'] ?? ''); ?>"
           placeholder="Enter stream URL">
    <small class="form-text text-muted">Add or update the stream URL when available</small>
</div> 