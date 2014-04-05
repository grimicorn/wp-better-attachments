module.exports =
  css:
    files: ["assets/css/scss/**/*.scss"]
    tasks: ["css"]
    options:
      spawn: false

  js:
    files: ["assets/js/src/**/*.js"]
    tasks: ["js"]
    options:
      spawn: false

  img:
    files: [
      "assets/**/*.{png,jpg,gif}"
      "!assets/img/sprite/**/*.{png,jpg,gif}"
      "!assets/img/sprite.png"
    ]
    tasks: ["imageoptim:all"]
    options:
      spawn: false

  sprite:
    files: ["assets/img/sprite/**/*.{png,jpg,gif}"]
    tasks: ["imageoptim:sprite", "sprite"]
    options:
      spawn: false

  livereload:
    options:
      livereload: true
      spawn: false
    files: [
      "assets/css/dist/*.css"
      "assets/js/dist/*.js"
      "**/*.php"
      "**/*.{png,jpg,gif}"
      "!**/node_modules/**"
    ]