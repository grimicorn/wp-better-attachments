module.exports =
  public:
    options:
      style: "compressed"
      sourcemap: true

    files: [
      expand: true
      cwd: "assets/css/scss/public"
      src: ["wpba.scss"]
      dest: "assets/css/dist"
      ext: ".min.css"
    ]

  publicDist:
    options:
      style: "compressed"
      sourcemap: false

    files: [
      expand: true
      cwd: "assets/css/scss/public"
      src: ["wpba.scss"]
      dest: "assets/css/dist"
      ext: ".min.css"
    ]


  admin:
    options:
      style: "compressed"
      sourcemap: true

    files: [
      expand: true
      cwd: "assets/css/scss/admin"
      src: ["wpba-admin.scss"]
      dest: "assets/css/dist"
      ext: ".min.css"
    ]

  adminDist:
    options:
      style: "compressed"
      sourcemap: false

    files: [
      expand: true
      cwd: "assets/css/scss/admin"
      src: ["wpba-admin.scss"]
      dest: "assets/css/dist"
      ext: ".min.css"
    ]