const selectors = {
    thumbnail: {
        input: '#thumbnail',
        preview: '#thumbnail-preview',
        removeBtn: '#thumbnail-remove',
        addBtn: '.upload-thumbnail',
        spinner: '#spinner-thumbnail'
    },
    gallery: {
        input: '#edit-images',
        wrapper: '#edit-images-wrapper',
        removeBtn: '.images-wrapper-item-remove',
        addBtn: '.add-images',
        spinner: '#spinner'
    }
};

const template = `
<div class='mb-4 col-md-6 images-wrapper-item'>
    <button class="btn btn-danger images-wrapper-item-remove"
        data-url="/ajax/images/_id_">
        <i class="fa-solid fa-minus"></i>
    </button>
    <img src='_url_' style='width: 100%' />
</div>
`

$(document).ready(function () {
    $(document).on('click', selectors.gallery.addBtn, function (e) {
        e.preventDefault();
        $(selectors.gallery.input).click();
    });

    $(document).on('click', selectors.thumbnail.addBtn, function (e) {
        e.preventDefault();
        $(selectors.thumbnail.input).click();
    });

    $(selectors.thumbnail.input).on('change', function(e) {
        e.preventDefault();
        const uploadUrl = $(selectors.thumbnail.addBtn).data('upload');
        const formData = new FormData();

        $(selectors.thumbnail.spinner).removeClass('d-none');
        $(selectors.thumbnail.addBtn).addClass('disabled');

        formData.append(`thumbnail`, this.files[0], this.files[0].name);

        axios.post(
            uploadUrl,
            formData,
            {
                headers: { "Content-Type": "multipart/form-data" }
            }
        ).then((response) => {

        }).catch((error) => {

        })
    });

    $(selectors.gallery.input).on('change', function(e) {
        e.preventDefault();
        const uploadUrl = $(selectors.gallery.addBtn).data('upload');
        const formData = new FormData();

        $(selectors.gallery.spinner).removeClass('d-none');
        $(selectors.gallery.addBtn).addClass('disabled');

        for(let i = 0; i < this.files.length; i++) {
            formData.append(`images[${i}]`, this.files[i], this.files[i].name);
        }

        axios.post(
            uploadUrl,
            formData,
            {
                headers: { "Content-Type": "multipart/form-data" }
            }
        ).then((response) => {
            if (response.data?.length > 0) {
                for (const key in response.data) {
                    const imageBlock = template
                        .replace('_url_', response.data[key].url)
                        .replace('_id_', response.data[key].id)

                    $(selectors.gallery.wrapper).append(imageBlock)
                }
            }

        }).catch((error) => {
            console.error(error);
            iziToast.warning({
                message: error.data.message,
                position: 'topRight'
            });
        }).finally(() => {
            $(selectors.spinner).addClass('d-none');
            $(selectors.addBtn).removeClass('disabled');
        })
    });

    $(selectors.gallery.removeBtn).on('click', function (e) {
        e.preventDefault();

        const $btn = $(this);

        $btn.addClass('disabled');

        axios.delete(
            $btn.data('url'),
            {
                responseType: 'json'
            }
        ).then((response) => {
            iziToast.success({
                message: response.data.message,
                position: 'topRight'
            });
            $btn.parent().remove();
        }).catch((error) => {
            console.error(error);
            iziToast.warning({
                message: error.data.message,
                position: 'topRight'
            });
            $btn.removeClass('disabled');
        })
    })
});
