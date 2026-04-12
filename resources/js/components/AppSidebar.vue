<script setup lang="ts">
import type { SidebarProps } from '@/components/ui/sidebar'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
  BookOpen,
  Bot,
  Command,
  Frame,
  LifeBuoy,
  Map,
  PieChart,
  Send,
  Settings2,
  SquareTerminal,
  LayoutDashboard,
  Users,
  CalendarDays,
  FolderArchive,
  MessageSquare,
  FileText
} from "lucide-vue-next"

import NavMain from '@/components/NavMain.vue'
import NavProjects from '@/components/NavProjects.vue'
import NavSecondary from '@/components/NavSecondary.vue'
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'

const props = withDefaults(defineProps<SidebarProps>(), {
  variant: "inset",
})

const page = usePage()
const auth = computed(() => page.props.auth)
const isAccreditor = computed(() => auth.value?.is_accreditor)

const dynamicNavMain = computed(() => {
  if (isAccreditor.value) {
    return [
      {
        title: "Accreditation",
        url: "/accreditor/dashboard?tab=active",
        icon: CalendarDays,
        isActive: true,
      },
      {
        title: "Archives",
        url: "/accreditor/dashboard?tab=archives",
        icon: FolderArchive,
      },
      {
        title: "Messages",
        url: "#",
        icon: MessageSquare,
        items: [
          { title: "Comments", url: "#" },
          { title: "Notifications", url: "#" },
        ]
      }
    ]
  }

  // Default / System User Navigation
  return [
    {
      title: "Dashboard",
      url: "/dashboard",
      icon: LayoutDashboard,
      isActive: true,
    },
    {
      title: "User Management",
      url: "/user-management",
      icon: Users,
    },
    {
      title: "Events",
      url: "/events",
      icon: CalendarDays,
    },
    {
      title: "Repository",
      url: "/repository",
      icon: FileText,
    },
  ]
})

const dynamicProjects = computed(() => {
  if (isAccreditor.value) return []
  
  return [
    {
      name: "Monitoring",
      url: "#",
      icon: Frame,
    },
    {
      name: "Reports",
      url: "#",
      icon: PieChart,
    },
  ]
})

const navSecondary = [
  {
    title: "Support",
    url: "#",
    icon: LifeBuoy,
  },
  {
    title: "Feedback",
    url: "#",
    icon: Send,
  },
]
</script>

<template>
  <Sidebar v-bind="props">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <a href="/">
              <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground font-bold">
                C
              </div>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-bold">CvSU Archives</span>
                <span class="truncate text-xs">Accreditation System</span>
              </div>
            </a>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="dynamicNavMain" />
      <NavProjects :projects="dynamicProjects" />
      <NavSecondary :items="navSecondary" class="mt-auto" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser :user="auth.user" />
    </SidebarFooter>
  </Sidebar>
</template>
