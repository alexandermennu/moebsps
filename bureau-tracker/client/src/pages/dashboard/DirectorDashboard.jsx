import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api/client';
import StatCard from '../../components/StatCard';
import { StatusBadge, formatDate, daysUntil } from '../../utils/helpers';
import { Activity, AlertTriangle, CheckCircle, TrendingUp } from 'lucide-react';

export default function DirectorDashboard() {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetch() {
      try {
        const res = await api.get('/dashboard/director');
        setData(res);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    }
    fetch();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-600" />
      </div>
    );
  }

  if (!data) return <p className="text-gray-500">Failed to load dashboard data.</p>;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Division Dashboard</h1>
        <p className="text-gray-500 mt-1">Overview of your division activities</p>
      </div>

      {/* Stats row */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          title="Active Activities"
          value={data.activeActivities?.length || 0}
          icon={Activity}
          color="blue"
        />
        <StatCard
          title="Overdue Activities"
          value={data.overdueActivities?.length || 0}
          icon={AlertTriangle}
          color="red"
        />
        <StatCard
          title="Completed"
          value={data.completedActivities || 0}
          icon={CheckCircle}
          color="green"
        />
        <StatCard
          title="Completion Rate"
          value={`${data.completionRate}%`}
          subtitle={`${data.completedActivities} of ${data.totalActivities}`}
          icon={TrendingUp}
          color="purple"
        />
      </div>

      {/* Overdue Activities */}
      {data.overdueActivities?.length > 0 && (
        <div className="card">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-red-700 flex items-center gap-2">
              <AlertTriangle size={18} />
              Overdue Activities
            </h2>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Activity</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Deadline</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Days Overdue</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {data.overdueActivities.map((act) => (
                  <tr key={act.id} className="hover:bg-red-50/50">
                    <td className="px-5 py-3 font-medium text-gray-900">{act.title}</td>
                    <td className="px-5 py-3 text-gray-600">{formatDate(act.deadline)}</td>
                    <td className="px-5 py-3">
                      <span className="badge-red">{Math.abs(daysUntil(act.deadline))} days</span>
                    </td>
                    <td className="px-5 py-3"><StatusBadge status={act.status} /></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Active Activities */}
      <div className="card">
        <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
          <h2 className="font-semibold text-gray-800">Active Activities</h2>
          <Link to="/activities" className="text-sm text-primary-600 hover:text-primary-800 font-medium">
            View All →
          </Link>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-gray-50">
              <tr>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Activity</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Assigned To</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Deadline</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {(data.activeActivities || []).slice(0, 10).map((act) => (
                <tr key={act.id} className="hover:bg-gray-50">
                  <td className="px-5 py-3 font-medium text-gray-900">{act.title}</td>
                  <td className="px-5 py-3 text-gray-600">
                    {(act.assigned_users || []).join(', ') || '—'}
                  </td>
                  <td className="px-5 py-3 text-gray-600">{formatDate(act.deadline)}</td>
                  <td className="px-5 py-3"><StatusBadge status={act.status} /></td>
                </tr>
              ))}
              {(!data.activeActivities || data.activeActivities.length === 0) && (
                <tr>
                  <td colSpan={4} className="text-center py-8 text-gray-400">
                    No active activities
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
