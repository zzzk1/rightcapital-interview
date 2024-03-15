<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h3 {
            margin-top: 0;
        }
        ul {
            padding: 0;
            list-style-type: none;
        }
        li {
            margin-bottom: 5px;
        }
        .hidden {
            display: none;
        }
        .tag {
            display: inline-block;
            background-color: #f0f0f0;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
        }
        .tag-remove {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Note Details</h1>
        <form id="note-form" action="/notepad/{{ $note->id }}" method="post">
            @csrf
            @method('POST')
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" value="{{ $note->title }}"><br><br>
            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="5">{{ $note->content }}</textarea><br><br>
            
            <h3>Tags:</h3>
            <div id="tag-container">
                @foreach ($tags as $tag)
                    <span class="tag">{{ $tag->name }} <span class="tag-remove" onclick="removeTag(this, '{{ $tag->id }}')">x</span></span>
                @endforeach
            </div>
            <input type="text" id="tag-input" placeholder="Add a tag">
            <button type="button" onclick="addTag()">Add Tag</button><br><br>
            
            <button type="submit">Save</button>
        </form>
    </div>

    <!-- TODO: use a drop down box to select tag -->
    <script>
        function addTag() {
            var tagInput = document.getElementById('tag-input');
            var tagName = tagInput.value.trim();
            if (tagName !== '') {
                var tagContainer = document.getElementById('tag-container');
                var tagSpan = document.createElement('span');
                tagSpan.classList.add('tag');
                tagSpan.innerHTML = tagName + ' <span class="tag-remove" onclick="removeTag(this)">x</span>';
                tagContainer.appendChild(tagSpan);
                tagInput.value = '';
            }
        }
    </script>
</body>
</html>
