<script setup lang="ts">
import { computed, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue: File | null;
        label?: string;
        previewUrl?: string | null;
        aspectRatio?: number;
        outputWidth?: number;
        outputHeight?: number | null;
        roundedPreview?: boolean;
    }>(),
    {
        label: 'Gambar',
        previewUrl: null,
        aspectRatio: 16 / 10,
        outputWidth: 1200,
        outputHeight: null,
        roundedPreview: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: File | null];
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const sourceUrl = ref<string | null>(props.previewUrl);
const processedUrl = ref<string | null>(props.previewUrl);
const hasNewImage = ref(false);
const originalSize = ref(0);
const resultSize = ref(0);
const fileName = ref('gambar.jpg');

const zoom = ref(1);
const positionX = ref(0);
const positionY = ref(0);
const targetWidth = ref(props.outputWidth);
const quality = ref(82);

const targetHeight = computed(() =>
    props.outputHeight ?? Math.round(targetWidth.value / props.aspectRatio),
);

const sizeLabel = (bytes: number) => {
    if (!bytes) return '-';
    if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;

    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
};

const outputInfo = computed(
    () => `${targetWidth.value} x ${targetHeight.value}px`,
);

const loadImage = (url: string) =>
    new Promise<HTMLImageElement>((resolve, reject) => {
        const image = new Image();
        image.onload = () => resolve(image);
        image.onerror = reject;
        image.src = url;
    });

const objectUrl = (blob: Blob) => {
    if (processedUrl.value?.startsWith('blob:')) {
        URL.revokeObjectURL(processedUrl.value);
    }

    processedUrl.value = URL.createObjectURL(blob);
};

const renderImage = async () => {
    if (!sourceUrl.value || !hasNewImage.value) return;

    const image = await loadImage(sourceUrl.value);
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    if (!context) return;

    canvas.width = targetWidth.value;
    canvas.height = targetHeight.value;

    const baseScale = Math.max(
        canvas.width / image.width,
        canvas.height / image.height,
    );
    const scale = baseScale * zoom.value;
    const drawWidth = image.width * scale;
    const drawHeight = image.height * scale;
    const maxOffsetX = Math.max(0, (drawWidth - canvas.width) / 2);
    const maxOffsetY = Math.max(0, (drawHeight - canvas.height) / 2);
    const offsetX = (positionX.value / 100) * maxOffsetX;
    const offsetY = (positionY.value / 100) * maxOffsetY;
    const x = (canvas.width - drawWidth) / 2 + offsetX;
    const y = (canvas.height - drawHeight) / 2 + offsetY;

    context.fillStyle = '#fffaf2';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.drawImage(image, x, y, drawWidth, drawHeight);

    const blob = await new Promise<Blob | null>((resolve) =>
        canvas.toBlob(resolve, 'image/jpeg', quality.value / 100),
    );

    if (!blob) return;

    const safeName = fileName.value.replace(/\.[^.]+$/, '') || 'gambar';
    const file = new File([blob], `${safeName}.jpg`, {
        type: 'image/jpeg',
        lastModified: Date.now(),
    });

    resultSize.value = file.size;
    objectUrl(blob);
    emit('update:modelValue', file);
};

const chooseFile = () => fileInput.value?.click();

const onFileChange = async (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) return;

    if (sourceUrl.value?.startsWith('blob:')) {
        URL.revokeObjectURL(sourceUrl.value);
    }

    hasNewImage.value = true;
    originalSize.value = file.size;
    fileName.value = file.name;
    sourceUrl.value = URL.createObjectURL(file);
    zoom.value = 1;
    positionX.value = 0;
    positionY.value = 0;
    targetWidth.value = props.outputWidth;
    quality.value = 82;

    await renderImage();
};

watch(
    [zoom, positionX, positionY, targetWidth, quality],
    () => {
        void renderImage();
    },
);

watch(
    () => props.previewUrl,
    (value) => {
        if (!hasNewImage.value) {
            sourceUrl.value = value;
            processedUrl.value = value;
        }
    },
);
</script>

<template>
    <div class="grid gap-3">
        <div class="flex items-center justify-between gap-3">
            <span class="text-sm font-semibold text-[#5f5549] dark:text-[#c8bdaa]">
                {{ label }}
            </span>
            <button
                type="button"
                class="inline-flex min-h-9 items-center gap-2 rounded-full bg-[#3d2b1f] px-4 text-xs font-bold text-[#f7f2e8]"
                @click="chooseFile"
            >
                <span class="material-symbols-outlined text-base">upload</span>
                Pilih Gambar
            </button>
        </div>

        <div class="rounded-lg border border-dashed border-[#d6b35a]/40 bg-[#fffaf2] p-4 dark:bg-[#1d2521]">
            <div
                class="mx-auto overflow-hidden bg-[#f1ece3] dark:bg-[#28322e]"
                :class="roundedPreview ? 'h-32 w-32 rounded-full' : 'aspect-[16/10] w-full rounded-lg'"
            >
                <img
                    v-if="processedUrl"
                    :src="processedUrl"
                    class="h-full w-full object-cover"
                    alt="Pratinjau gambar"
                />
                <div
                    v-else
                    class="flex h-full w-full flex-col items-center justify-center text-[#7a6a58]"
                >
                    <span class="material-symbols-outlined mb-1 text-4xl">
                        add_photo_alternate
                    </span>
                    <span class="text-xs">Belum ada gambar</span>
                </div>
            </div>

            <div v-if="hasNewImage" class="mt-4 grid gap-3">
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="grid gap-1 text-xs font-bold text-[#7a6a58]">
                        Zoom
                        <input v-model.number="zoom" type="range" min="1" max="2.6" step="0.05" />
                    </label>
                    <label class="grid gap-1 text-xs font-bold text-[#7a6a58]">
                        Resolusi
                        <input v-model.number="targetWidth" type="range" min="640" max="1600" step="80" />
                    </label>
                    <label class="grid gap-1 text-xs font-bold text-[#7a6a58]">
                        Posisi X
                        <input v-model.number="positionX" type="range" min="-100" max="100" step="1" />
                    </label>
                    <label class="grid gap-1 text-xs font-bold text-[#7a6a58]">
                        Posisi Y
                        <input v-model.number="positionY" type="range" min="-100" max="100" step="1" />
                    </label>
                </div>
                <label class="grid gap-1 text-xs font-bold text-[#7a6a58]">
                    Kualitas Kompresi
                    <input v-model.number="quality" type="range" min="58" max="92" step="1" />
                </label>
                <div class="grid gap-2 rounded-lg bg-[#f1ece3] p-3 text-xs text-[#6b4226] dark:bg-[#28322e] dark:text-[#c8bdaa] sm:grid-cols-3">
                    <span>Asli: <strong>{{ sizeLabel(originalSize) }}</strong></span>
                    <span>Hasil: <strong>{{ sizeLabel(resultSize) }}</strong></span>
                    <span>Ukuran: <strong>{{ outputInfo }}</strong></span>
                </div>
            </div>
        </div>

        <input
            ref="fileInput"
            type="file"
            class="hidden"
            accept="image/*"
            @change="onFileChange"
        />
    </div>
</template>
