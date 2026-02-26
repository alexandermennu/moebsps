import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import Layout from './components/Layout';
import ProtectedRoute from './components/ProtectedRoute';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import WeeklyUpdates from './pages/WeeklyUpdates';
import WeeklyPlans from './pages/WeeklyPlans';
import Activities from './pages/Activities';
import AdminUsers from './pages/AdminUsers';
import AdminSettings from './pages/AdminSettings';

export default function App() {
  const { loading, isAuthenticated } = useAuth();

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-gray-50">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto" />
          <p className="text-gray-500 mt-4">Loading...</p>
        </div>
      </div>
    );
  }

  return (
    <Routes>
      {/* Public */}
      <Route
        path="/login"
        element={isAuthenticated ? <Navigate to="/dashboard" replace /> : <Login />}
      />

      {/* Protected - Wrapped in Layout */}
      <Route
        element={
          <ProtectedRoute>
            <Layout />
          </ProtectedRoute>
        }
      >
        <Route path="/dashboard" element={<Dashboard />} />

        <Route
          path="/weekly-updates"
          element={
            <ProtectedRoute roles={['division_director', 'bureau_head', 'admin']}>
              <WeeklyUpdates />
            </ProtectedRoute>
          }
        />

        <Route
          path="/weekly-plans"
          element={
            <ProtectedRoute roles={['division_director', 'bureau_head', 'admin']}>
              <WeeklyPlans />
            </ProtectedRoute>
          }
        />

        <Route path="/activities" element={<Activities />} />

        <Route
          path="/admin/users"
          element={
            <ProtectedRoute roles={['admin']}>
              <AdminUsers />
            </ProtectedRoute>
          }
        />

        <Route
          path="/admin/settings"
          element={
            <ProtectedRoute roles={['admin']}>
              <AdminSettings />
            </ProtectedRoute>
          }
        />
      </Route>

      {/* Catch-all */}
      <Route path="*" element={<Navigate to={isAuthenticated ? '/dashboard' : '/login'} replace />} />
    </Routes>
  );
}
