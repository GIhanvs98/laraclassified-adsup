<form wire:submit="save">

    <input type="text" wire:model="title" placeholder="Post title" />

    <div>
        <div wire:ignore class="w-full block">
            <textarea id="content" wire:model="content" placeholder="Your Ad Description..." rows="6" cols="40" style="font-size: 14px;">
                {!! $content !!}
            </textarea>
        </div>
    </div>

    <input type="submit" value="Submit now">

    @script
    
        <script type="module">

            document.addEventListener('livewire:initialized', () => {
                
                $(function() {

                    // Function to strip script and style tags with content
                    function stripScriptStyle(html) {
                        // Decode HTML entities
                        var decodedHtml = he.decode(html);

                        // Create a temporary div element
                        var tempDiv = document.createElement('div');
                        tempDiv.innerHTML = decodedHtml;

                        // Remove script tags and their content
                        var scriptTags = tempDiv.querySelectorAll('script');
                        scriptTags.forEach(function (scriptTag) {
                            scriptTag.parentNode.removeChild(scriptTag);
                        });

                        // Remove style tags and their content
                        var styleTags = tempDiv.querySelectorAll('style');
                        styleTags.forEach(function (styleTag) {
                            styleTag.parentNode.removeChild(styleTag);
                        });

                        // Get the cleaned HTML
                        var cleanedHtml = tempDiv.innerHTML;

                        // Encode HTML entities again
                        var encodedHtml = he.encode(cleanedHtml);

                        return encodedHtml;
                    }

                    tinymce.init({
                        selector: '#content'
                        , plugins: "emoticons autoresize"
                        , plugins: 'searchreplace table visualblocks wordcount emoticons autoresize'
                        /* , toolbar: "undo redo | bold italic underline | subscript superscript | bullist numlist | link emoticons | removeformat" */
                        , toolbar: "undo redo | emoticons"
                        , toolbar_location: "top"
                        , menubar: false
                        , statusbar: false,
                        min_height: 200
                        , setup: function(editor) {
                            editor.on('init change', function() {
                                editor.save();
                            });
                            editor.on('init', function(e) {

                                let originalString = $("#content").html();

                                let decodedString = he.decode(originalString);

                                editor.setContent(decodedString);

                            });
                            editor.on('change', function(e) {

                                let originalString = editor.getContent();

                                let encodedString = stripScriptStyle(originalString);

                                $wire.$set('content', encodedString);
                            
                            });
                        }
                    });

                });

            });

        </script>

    @endscript

</form>