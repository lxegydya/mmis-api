<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500&display=swap');

        *{
            margin: 0;
            padding: 0;
        }
    </style>

</head>
<body>
    <a href="#" id="download-btn">Download</a>
    <canvas id="my_canvas" style="border:2px solid;"></canvas>
    
    <script>
        var canvas = document.getElementById('my_canvas');
        var ctx = canvas.getContext('2d');
        var imageObj = new Image();
        ctx.canvas.width = 1754;
        ctx.canvas.height = 1240;

        imageObj.onload = function() {
            drawImage()
            setName()
            setDesc()
        };

        let drawImage = () => {
            ctx.drawImage(imageObj, 0, 0, 1754, 1240);
        }

        let setName = () => {
            ctx.font = '500 56pt Montserrat'
            ctx.fillStyle = '#FE4F65'
            ctx.fillText('Egy Dya Hermawan', 120, 550)
        }

        let setDesc = () => {
            ctx.font = '300 18pt Montserrat'
            ctx.fillStyle = '#4A4F52'
            ctx.fillText('Congratulations You have completed The Web Programming Fullstack', 126, 630)
            ctx.fillText('with LearningX. Your Final Grade is A with 841 Accumulation Points', 126, 665)
        }

        imageObj.src = '{{ URL::to('/') }}/certification_template/base_template.svg';

        document.getElementById('download-btn').addEventListener('click', () => {
            let url = canvas.toDataURL()
            document.getElementById('download-btn').href = url
            document.getElementById('download-btn').download = true
        })
    </script>
</body>
</html>