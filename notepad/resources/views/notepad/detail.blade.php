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
    </style>
</head>
<body>
    <div class="container">
        <h1>Note Details</h1>
        <article>
            <h2>{{ $note->title }}</h2>
            <h2>{{ $note->content }}</h2>
            
            <h3>Tags:</h3>
            <ul>
                @foreach ($tags as $tag)
                    <li>{{ $tag->name }}</li>
                @endforeach
            </ul>
        </article>
    </div>
</body>
</html>
