@extends('layouts.app')

@section('title', 'Обработка файлов')

@section('content')
    <div class="flex h-screen items-center justify-center">
        <form class="flex h-min flex-col items-center gap-5" x-data="{
            processing: false,
            fileName: '',
        
            init() {
                if (localStorage.getItem('text1')) {
                    this.$refs.text1.value = localStorage.getItem('text1')
                }
                if (localStorage.getItem('text2')) {
                    this.$refs.text2.value = localStorage.getItem('text2')
                }
                if (localStorage.getItem('text3')) {
                    this.$refs.text3.value = localStorage.getItem('text3')
                }
                if (localStorage.getItem('number')) {
                    this.$refs.number.value = localStorage.getItem('number')
                }
            },
            submit(e) {
                this.processing = true
        
                this.saveValues()
        
                axios.post('/checkscript', new FormData(e.target), { responseType: 'blob' })
                    .then(resp => {
                        let blob = resp.data
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.setAttribute('download', this.fileName + '.zip');
                        link.click();
                        URL.revokeObjectURL(link.href);
                    })
                    .catch(err => console.error(err))
                    .finally(() => this.processing = false)
            },
            saveValues() {
                localStorage.setItem('text1', this.$refs.text1.value)
                localStorage.setItem('text2', this.$refs.text2.value)
                localStorage.setItem('text3', this.$refs.text3.value)
                localStorage.setItem('number', this.$refs.number.value)
            },
            getFileName(event) {
                const files = event.target.files;
                const fileName = files[0].name;
                let name = fileName.split('.');
                name.pop();
                this.fileName = name.join('.');
            },
        }" @submit.prevent="submit">
            <input type="file" name='file' x-ref="file" @change="getFileName">
            <div class="flex gap-2">
                <textarea name="text1" class="border" x-ref="text1"></textarea>
                <textarea name="text2" class="border" x-ref="text2"></textarea>
                <textarea name="text3" class="border" x-ref="text3"></textarea>
            </div>

            <input type="number" step="1" min="1" value="50" name="number" x-ref="number">

            <button class="flex min-w-[200px] items-center justify-center gap-1 border p-2 transition hover:bg-green-100"
                :disabled="processing">
                <span x-show="!processing">submit</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="24" height="24"
                    x-show="processing" x-cloak>
                    <radialGradient id="a9" cx=".66" fx=".66" cy=".3125" fy=".3125"
                        gradientTransform="scale(1.5)">
                        <stop offset="0" stop-color="#000000"></stop>
                        <stop offset=".3" stop-color="#000000" stop-opacity=".9"></stop>
                        <stop offset=".6" stop-color="#000000" stop-opacity=".6"></stop>
                        <stop offset=".8" stop-color="#000000" stop-opacity=".3"></stop>
                        <stop offset="1" stop-color="#000000" stop-opacity="0"></stop>
                    </radialGradient>
                    <circle transform-origin="center" fill="none" stroke="url(#a9)" stroke-width="15"
                        stroke-linecap="round" stroke-dasharray="200 1000" stroke-dashoffset="0" cx="100"
                        cy="100" r="70">
                        <animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="2"
                            values="360;0" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform>
                    </circle>
                    <circle transform-origin="center" fill="none" opacity=".2" stroke="#000000" stroke-width="15"
                        stroke-linecap="round" cx="100" cy="100" r="70"></circle>
                </svg>
            </button>
        </form>
    </div>
@endsection
