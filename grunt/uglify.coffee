module.exports =
  options:
    mangle: false

  public:
    options:
      sourceMap: "wpba.min.js.map"

    files:
      "assets/js/dist/wpba.min.js": [
        "assets/js/src/public/wpba.js"
      ]

  admin:
    options:
      sourceMap: "wpba-admin.min.js.map"

    files:
      "assets/js/dist/wpba-admin.min.js": [
        "assets/js/src/admin/wpba-meta.js"
        "assets/js/src/admin/wpba-admin.js"
      ]

  dist:
    files:
      "assets/js/dist/wpba.min.js": "assets/js/dist/wpba.min.js"
      "assets/js/dist/wpba-admin.min.js": "assets/js/dist/wpba-admin.min.js"