export default {
  mounted(el) {
    const header = el.querySelector('.modal-header')
    if (!header) return

    header.style.cursor = 'move'

    let offsetX = 0
    let offsetY = 0
    let isDragging = false

    // Guardar el ancho original
    const originalWidth = el.offsetWidth + 'px'
    el.style.width = originalWidth
    el.style.maxWidth = 'none' // Evita que Bootstrap limite el tamaño
    el.style.position = 'fixed' // Posición fija para arrastrar

    header.addEventListener('mousedown', (e) => {
      isDragging = true
      const rect = el.getBoundingClientRect()
      offsetX = e.clientX - rect.left
      offsetY = e.clientY - rect.top

      const onMouseMove = (e) => {
        if (!isDragging) return
        el.style.margin = '0'
        el.style.top = `${e.clientY - offsetY}px`
        el.style.left = `${e.clientX - offsetX}px`
      }

      const onMouseUp = () => {
        isDragging = false
        document.removeEventListener('mousemove', onMouseMove)
        document.removeEventListener('mouseup', onMouseUp)
      }

      document.addEventListener('mousemove', onMouseMove)
      document.addEventListener('mouseup', onMouseUp)
    })

    // Prevenir cambio de cursor dentro del modal
    el.querySelectorAll('*').forEach(child => {
      child.style.cursor = 'default'
    })
  }
}
