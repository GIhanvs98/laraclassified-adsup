<div class="w-full">

    <div class="input-group" style="margin-top: 20px;">
        <div class="text-xs text-gray-500">Description</div>
        <div wire:ignore class="w-full mt-1">
            <textarea id="content" wire:model="content" placeholder="Your Ad Description..." rows="6" cols="40" class="border w-full mt-1 mb-1 p-2 h-48">
                {!! $content !!}
            </textarea>
        </div>
        <script>
            ClassicEditor
                .create(document.querySelector(`#content`), {
                    toolbar: [
                        "heading"
                        , "|"
                        , "bold"
                        , "italic"
                        , "link"
                        , "bulletedList"
                        , "numberedList"
                        , "|"
                        , "indent"
                        , "outdent"
                        , "|"
                        , "codeBlock"
                        , "blockQuote"
                        , "insertTable"
                        , "mediaEmbed"
                        , "undo"
                        , "redo"
                    , ]
                , })
                .then(editor => {

                    editor.model.document.on('change:data', () => {
                        @this.set('content', editor.getData());
                    });

                })
                .catch(error => {
                    console.error(error);
                });

        </script>

        @error('content') <div class="text-xs text-red-500">{{ $message }}</div> @enderror
    </div>

    <div>{{ $content }}</div>


</div>
