import { useState, useEffect } from 'react';
import api from '../api/client';
import { Settings, Save, CheckCircle } from 'lucide-react';

export default function AdminSettings() {
  const [settings, setSettings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchSettings();
  }, []);

  async function fetchSettings() {
    try {
      const data = await api.get('/settings');
      setSettings(data.settings || []);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  }

  function updateSetting(key, value) {
    setSettings((prev) =>
      prev.map((s) => (s.key === key ? { ...s, value } : s))
    );
    setSaved(false);
  }

  async function handleSave() {
    setError('');
    setSaving(true);
    setSaved(false);

    try {
      await api.put('/settings', {
        settings: settings.map((s) => ({ key: s.key, value: s.value })),
      });
      setSaved(true);
      setTimeout(() => setSaved(false), 3000);
    } catch (err) {
      setError(err.message);
    } finally {
      setSaving(false);
    }
  }

  const settingDescriptions = {
    overdue_threshold_days: 'Number of days past deadline before marking as overdue and sending the first notification.',
    escalation_threshold_days: 'Number of days past deadline before escalating to Bureau Head and Minister.',
    reminder_frequency_hours: 'How often (in hours) to re-send overdue reminders.',
    escalation_enabled: 'Enable or disable escalation notifications to Bureau Head and Minister.',
    deadline_warning_days: 'Number of days before a deadline to send an approaching-deadline warning.',
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-600" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">System Settings</h1>
          <p className="text-gray-500 mt-1">Configure overdue thresholds, escalation, and reminders</p>
        </div>
        <button
          onClick={handleSave}
          disabled={saving}
          className="btn-primary flex items-center gap-2"
        >
          {saved ? (
            <>
              <CheckCircle size={18} />
              Saved!
            </>
          ) : (
            <>
              <Save size={18} />
              {saving ? 'Saving...' : 'Save Settings'}
            </>
          )}
        </button>
      </div>

      {error && (
        <div className="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">{error}</div>
      )}

      <div className="card">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="font-semibold text-gray-800 flex items-center gap-2">
            <Settings size={18} />
            Configuration
          </h2>
        </div>
        <div className="divide-y divide-gray-100">
          {settings.map((setting) => (
            <div key={setting.key} className="px-5 py-5">
              <div className="flex items-start justify-between gap-6">
                <div className="flex-1">
                  <h3 className="font-medium text-gray-900">{setting.label || setting.key}</h3>
                  <p className="text-sm text-gray-500 mt-0.5">
                    {settingDescriptions[setting.key] || `Configuration key: ${setting.key}`}
                  </p>
                </div>
                <div className="w-48 flex-shrink-0">
                  {setting.key === 'escalation_enabled' ? (
                    <label className="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        checked={setting.value === 'true'}
                        onChange={(e) =>
                          updateSetting(setting.key, e.target.checked ? 'true' : 'false')
                        }
                        className="sr-only peer"
                      />
                      <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4
                                      peer-focus:ring-primary-100 rounded-full peer
                                      peer-checked:after:translate-x-full peer-checked:after:border-white
                                      after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                      after:bg-white after:border-gray-300 after:border after:rounded-full
                                      after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600">
                      </div>
                      <span className="ml-3 text-sm text-gray-700">
                        {setting.value === 'true' ? 'Enabled' : 'Disabled'}
                      </span>
                    </label>
                  ) : (
                    <input
                      type="number"
                      value={setting.value}
                      onChange={(e) => updateSetting(setting.key, e.target.value)}
                      min="1"
                      className="input-field"
                    />
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
