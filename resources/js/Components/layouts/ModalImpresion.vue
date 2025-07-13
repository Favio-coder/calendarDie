<!-- ModalImpresion.vue -->
<template>
    <div v-if="isModalOpen" class="modal fade show d-block" aria-modal="true" role="dialog">
        <div class="modal-dialog" style="max-width: 800px; height: 100px;">
            <div class="modal-content">
                <div class="modal-header titulo-agend-actividad  text-white">
                    <h5 class="modal-title">Vista previa de impresión</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <iframe v-if="pdfBlobUrl" :src="pdfBlobUrl" frameborder="0"
                        style="width: 100%; height: 100%;"></iframe>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" @click="imprimir">Imprimir</button>
                    <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ModalImpresion",
    props: {
        pdfBlobUrl: String, 
    },
    data() {
        return {
            isModalOpen: false,
        };
    },
    methods: {
        openModal() {
            this.isModalOpen = true;
            document.body.classList.add("modal-open");
        },
        closeModal() {
            this.isModalOpen = false;
            document.body.classList.remove("modal-open");
            this.$emit("close-modalImpresion");
        },
        imprimir() {
            const iframe = document.querySelector("iframe");
            if (iframe && iframe.contentWindow) {
                iframe.focus();
                iframe.contentWindow.print();
            }
        }

    },
};
</script>

<style scoped>
.modal-open {
    overflow: hidden !important;
}

.titulo-agend-actividad {
    background-color: #12BACA;
}

.btn-close {
    filter: invert(1) brightness(2);
}
</style>
