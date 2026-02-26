import { useState, useEffect } from 'react';
import api from '../../api/client';
import StatCard from '../../components/StatCard';
import { StatusBadge, formatDate } from '../../utils/helpers';
import {
  Activity, AlertTriangle, Users, Clock,
  BarChart3
} from 'lucide-react';
import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip,
  ResponsiveContainer, Cell
} from 'recharts';

const COLORS = ['#3b82f6', '#ef4444', '#22c55e', '#f59e0b', '#8b5cf6'];

export default function BureauHeadDashboard() {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetch() {
      try {
        const res = await api.get('/dashboard/bureau-head');
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

  if (!data) return <p className="text-gray-500">Failed to load dashboard.</p>;

  const totalActive = data.allActiveActivities?.length || 0;
  const totalOverdue = data.overdueByDivision?.reduce((s, d) => s + parseInt(d.overdue_count), 0) || 0;

  const chartData = (data.divisionSummary || []).map((d) => ({
    name: d.division_name?.length > 12 ? d.division_name.substring(0, 12) + '…' : d.division_name,
    Active: parseInt(d.active) || 0,
    Completed: parseInt(d.completed) || 0,
    Overdue: parseInt(d.overdue) || 0,
  }));

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Bureau Head Dashboard</h1>
        <p className="text-gray-500 mt-1">Overview across all divisions</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard title="Active Activities" value={totalActive} icon={Activity} color="blue" />
        <StatCard title="Total Overdue" value={totalOverdue} icon={AlertTriangle} color="red" />
        <StatCard
          title="Pending Updates"
          value={data.pendingUpdates?.length || 0}
          subtitle="Divisions without updates this week"
          icon={Clock}
          color="yellow"
        />
        <StatCard
          title="Divisions"
          value={data.divisionSummary?.length || 0}
          icon={Users}
          color="purple"
        />
      </div>

      {/* Division Performance Chart */}
      {chartData.length > 0 && (
        <div className="card p-5">
          <h2 className="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <BarChart3 size={18} />
            Division Performance
          </h2>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={chartData}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
              <XAxis dataKey="name" tick={{ fontSize: 12 }} />
              <YAxis tick={{ fontSize: 12 }} />
              <Tooltip />
              <Bar dataKey="Active" fill="#3b82f6" radius={[2, 2, 0, 0]} />
              <Bar dataKey="Completed" fill="#22c55e" radius={[2, 2, 0, 0]} />
              <Bar dataKey="Overdue" fill="#ef4444" radius={[2, 2, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Overdue by Division */}
        <div className="card">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-gray-800">Overdue by Division</h2>
          </div>
          <div className="p-5 space-y-3">
            {(data.overdueByDivision || []).length === 0 ? (
              <p className="text-gray-400 text-sm text-center py-4">No overdue activities 🎉</p>
            ) : (
              data.overdueByDivision.map((d) => (
                <div key={d.division_id} className="flex items-center justify-between">
                  <span className="text-sm text-gray-700">{d.division_name}</span>
                  <span className="badge-red">{d.overdue_count} overdue</span>
                </div>
              ))
            )}
          </div>
        </div>

        {/* Overdue by Individual */}
        <div className="card">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-gray-800">Overdue by Individual</h2>
          </div>
          <div className="p-5 space-y-3">
            {(data.overdueByIndividual || []).length === 0 ? (
              <p className="text-gray-400 text-sm text-center py-4">No overdue assignments</p>
            ) : (
              data.overdueByIndividual.map((p, i) => (
                <div key={i} className="flex items-center justify-between">
                  <span className="text-sm text-gray-700">{p.person}</span>
                  <span className="badge-red">{p.overdue_count} overdue</span>
                </div>
              ))
            )}
          </div>
        </div>
      </div>

      {/* Pending Updates */}
      {data.pendingUpdates?.length > 0 && (
        <div className="card">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-yellow-700 flex items-center gap-2">
              <Clock size={18} />
              Divisions Without Updates This Week
            </h2>
          </div>
          <div className="p-5">
            <div className="flex flex-wrap gap-2">
              {data.pendingUpdates.map((d) => (
                <span key={d.id} className="badge-yellow">{d.name}</span>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* All Active Activities Table */}
      <div className="card">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="font-semibold text-gray-800">All Active Activities</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-gray-50">
              <tr>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Activity</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Division</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Deadline</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {(data.allActiveActivities || []).slice(0, 15).map((act) => (
                <tr key={act.id} className="hover:bg-gray-50">
                  <td className="px-5 py-3 font-medium text-gray-900">{act.title}</td>
                  <td className="px-5 py-3 text-gray-600">{act.division_name}</td>
                  <td className="px-5 py-3 text-gray-600">{formatDate(act.deadline)}</td>
                  <td className="px-5 py-3"><StatusBadge status={act.status} /></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
