module.exports =
  assets:
    options:
      algorithm: "sha1"
      length: 4
      format: false
      rename: true

    files:
      "classes/class-wp-better-attachments.php": [
        "assets/js/dist/wpba-admin.min.js"
        "assets/css/dist/wpba-admin.min.css"
        "assets/js/dist/wpba.min.js"
        "assets/css/dist/wpba.min.css"
      ]