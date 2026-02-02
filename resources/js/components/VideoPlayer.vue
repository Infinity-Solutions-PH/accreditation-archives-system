<script setup>
    import { ref, computed, watch } from 'vue'

    defineProps({
        id: Number,
    })

    const videoEl = ref(null)
    const isPlaying = ref(false)
    const showControls = ref(true)

    const duration = ref(0)
    const currentTime = ref(0)
    const volume = ref(1)

    const togglePlay = () => {
    if (!videoEl.value) return

    if (videoEl.value.paused) {
        videoEl.value.play()
        isPlaying.value = true
    } else {
        videoEl.value.pause()
        isPlaying.value = false
    }
    }

    const onLoadedMetadata = () => {
        duration.value = videoEl.value.duration
    }

    const onTimeUpdate = () => {
        currentTime.value = videoEl.value.currentTime
    }

    const seek = (e) => {
    const rect = e.currentTarget.getBoundingClientRect()
    const percent = (e.clientX - rect.left) / rect.width
        videoEl.value.currentTime = percent * duration.value
    }

    const setVolume = () => {
        videoEl.value.volume = volume.value
    }

    const toggleMute = () => {
        volume.value = volume.value === 0 ? 1 : 0
        setVolume()
    }

    const toggleFullscreen = () => {
        if (!document.fullscreenElement) {
            videoEl.value.requestFullscreen()
        } else {
            document.exitFullscreen()
        }
    }

    const progress = computed(() =>
        duration.value ? (currentTime.value / duration.value) * 100 : 0
    )

    const formatTime = (time) => {
    const m = Math.floor(time / 60)
    const s = Math.floor(time % 60).toString().padStart(2, '0')
        return `${m}:${s}`
    }

    const currentTimeFormatted = computed(() => formatTime(currentTime.value))
    const durationFormatted = computed(() => formatTime(duration.value))
</script>

<template>
    <div
        class="flex-1 bg-black flex flex-col justify-center items-center relative group"
        @mousemove="showControls = true"
        @mouseleave="showControls = false">
        <video
            ref="videoEl"
            class="w-full h-full object-contain bg-black"
            preload="metadata"
            @timeupdate="onTimeUpdate"
            @loadedmetadata="onLoadedMetadata"
            @ended="isPlaying = false"
        >
            <source :src="route('videos.stream', { file: id })" type="video/mp4" />
            Your browser does not support the video tag.
        </video>

        <!-- PLAY BUTTON OVERLAY -->
        <button
            v-if="!isPlaying"
            @click="togglePlay"
            class="absolute z-10 flex items-center justify-center rounded-full size-20 bg-primary/80 text-white hover:scale-110 transition-transform"
        >
            <span class="material-symbols-outlined text-4xl">play_arrow</span>
        </button>

        <!-- CONTROLS -->
        <div
            v-show="showControls || !isPlaying"
            class="absolute inset-x-0 bottom-0 px-6 py-4 bg-gradient-to-t from-black/80 to-transparent pt-12 transition-opacity"
        >
        <!-- PROGRESS BAR -->
        <div
            class="flex h-6 items-center cursor-pointer mb-2"
            @click="seek"
        >
            <div class="relative w-full h-1.5 rounded-full bg-white/20">
            <div
                class="absolute left-0 top-0 h-full rounded-full bg-primary"
                :style="{ width: progress + '%' }"
            />
            <div
                class="absolute -top-1.5 size-4 rounded-full bg-white border-2 border-primary shadow-lg"
                :style="{ left: `calc(${progress}% - 8px)` }"
            />
            </div>
        </div>

        <!-- CONTROLS ROW -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
            <span
                class="material-symbols-outlined text-white cursor-pointer hover:text-primary"
                @click="togglePlay"
            >
                {{ isPlaying ? 'pause' : 'play_arrow' }}
            </span>

            <!-- VOLUME -->
            <div class="flex items-center gap-2">
                <span
                class="material-symbols-outlined text-white cursor-pointer"
                @click="toggleMute"
                >
                {{ volume === 0 ? 'volume_off' : 'volume_up' }}
                </span>
                <input
                    type="range"
                    min="0"
                    max="1"
                    step="0.01"
                    v-model.number="volume"
                    @input="setVolume"
                    class="w-20"
                />
            </div>

            <p class="text-white text-xs ml-2">
                {{ currentTimeFormatted }} / {{ durationFormatted }}
            </p>
            </div>

            <span
                class="material-symbols-outlined text-white cursor-pointer hover:text-primary"
                @click="toggleFullscreen"
                >
                fullscreen
            </span>
        </div>
        </div>  
    </div>
</template>