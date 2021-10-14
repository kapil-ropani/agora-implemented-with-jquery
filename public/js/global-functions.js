function serializeformData() {
    var formData = $("#form").serializeArray()
    var obj = {}
    for (var item of formData) {
      var key = item.name
      var val = item.value
      obj[key] = val
    }
    return obj
  }

  function validator(formData, fields) {
    var keys = Object.keys(formData)
    for (let key of keys) {
      if (fields.indexOf(key) != -1) {
        if (!formData[key]) {
          console.error("Please Enter " + key)
          alert("Please Enter " + key)
          return false
        }
      }
    }
    return true
  }
