import { NavLink } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import {
  LayoutDashboard, FileText, CalendarPlus, Activity,
  Users, Settings, X
} from 'lucide-react';

const navItems = {
  division_director: [
    { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { to: '/weekly-updates', label: 'Weekly Updates', icon: FileText },
    { to: '/weekly-plans', label: 'Weekly Plans', icon: CalendarPlus },
    { to: '/activities', label: 'My Activities', icon: Activity },
  ],
  bureau_head: [
    { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { to: '/activities', label: 'All Activities', icon: Activity },
    { to: '/weekly-updates', label: 'Weekly Updates', icon: FileText },
    { to: '/weekly-plans', label: 'Weekly Plans', icon: CalendarPlus },
  ],
  minister: [
    { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { to: '/activities', label: 'All Activities', icon: Activity },
  ],
  admin: [
    { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { to: '/weekly-updates', label: 'Weekly Updates', icon: FileText },
    { to: '/weekly-plans', label: 'Weekly Plans', icon: CalendarPlus },
    { to: '/activities', label: 'Activities', icon: Activity },
    { to: '/admin/users', label: 'Manage Users', icon: Users },
    { to: '/admin/settings', label: 'Settings', icon: Settings },
  ],
};

export default function Sidebar({ open, onClose }) {
  const { user } = useAuth();
  const items = navItems[user?.role] || [];

  return (
    <>
      {/* Mobile overlay */}
      {open && (
        <div
          className="fixed inset-0 z-30 bg-black/50 lg:hidden"
          onClick={onClose}
        />
      )}

      {/* Sidebar */}
      <aside
        className={`fixed top-0 left-0 z-40 h-full w-64 bg-white border-r border-gray-200 
                     transform transition-transform duration-200 ease-in-out
                     lg:translate-x-0 lg:static lg:z-auto
                     ${open ? 'translate-x-0' : '-translate-x-full'}`}
      >
        {/* Logo area */}
        <div className="flex items-center justify-between h-16 px-6 border-b border-gray-200">
          <div className="flex items-center gap-2">
            <span className="text-2xl">📋</span>
            <span className="font-bold text-gray-800 text-lg">BureauTrack</span>
          </div>
          <button
            onClick={onClose}
            className="lg:hidden p-1 rounded hover:bg-gray-100"
          >
            <X size={20} />
          </button>
        </div>

        {/* Navigation */}
        <nav className="p-4 space-y-1">
          {items.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              onClick={onClose}
              className={({ isActive }) =>
                `flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                 ${isActive
                   ? 'bg-primary-50 text-primary-700'
                   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                 }`
              }
            >
              <item.icon size={20} />
              {item.label}
            </NavLink>
          ))}
        </nav>

        {/* User info at bottom */}
        <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
              <span className="text-primary-700 font-semibold text-sm">
                {user?.name?.charAt(0)?.toUpperCase()}
              </span>
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-medium text-gray-800 truncate">{user?.name}</p>
              <p className="text-xs text-gray-500 truncate capitalize">
                {user?.role?.replace('_', ' ')}
              </p>
            </div>
          </div>
        </div>
      </aside>
    </>
  );
}
